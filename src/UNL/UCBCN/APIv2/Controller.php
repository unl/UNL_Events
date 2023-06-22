<?php
namespace UNL\UCBCN\APIv2;

class Controller {
    public $options = array();
    public $request_data = array();
    public $output = array(
        'status' => 200,
        'message' => 'success',
        'data' => 'null',
    );
    public static $url = '/api/v2/';
    
    public function __construct($options = array()) {
        $this->options = $options + $this->options;

        try {
            $this->getRequestData();
            $this->run();
        } catch (ValidationException $e) {
            $this->output['status'] = 400;
            $this->output['message'] = $e->getMessage();
        } catch (NotFoundException $e) {
            $this->output['status'] = 404;
            $this->output['message'] = $e->getMessage();
        }  catch (InvalidMethodException $e) {
            $this->output['status'] = 405;
            $this->output['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->output['status'] = 500;
            $this->output['message'] = $e->getMessage();
        }
    }

    public function run()
    {
        if (!isset($this->options['model'])) {
            throw new NotFoundException('Not Found');
        }

        $model = new $this->options['model']($this->options);

        //check if model needs auth based on method

        $result = $model->run($_SERVER['REQUEST_METHOD'], $this->request_data);

        $this->output['data'] = $result;
    }

    public function checkAuthToken(): bool
    {
        $token = NULL;
        $token = array_key_exists('api_token', $this->request_data) ? $this->request_data['api_token'] : '';
        $test_output['token'] = $token;

        return true;
    }

    public function checkAuthCookie(): bool
    {
        return true;
    }

    public function getRequestData(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->request_data = $_POST;
        } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->request_data = $_GET;
        } else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            parse_str(file_get_contents('php://input'), $_PUT);
            $this->request_data = $_PUT;
        } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            parse_str(file_get_contents('php://input'), $_DELETE);
            $this->request_data = $_DELETE;
        } else {
            throw new InvalidMethodException('Method '. $_SERVER['REQUEST_METHOD']. ' is invalid.');
        }
    }
}
