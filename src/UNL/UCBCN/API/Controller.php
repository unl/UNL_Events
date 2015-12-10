<?php
namespace UNL\UCBCN\API;

use UNL\UCBCN\Calendar;

class Controller {
    public $options = array();
    public $output = NULL;
    public static $url = '/api/';
    
    public function __construct($options = array()) {
        $this->options = $options + $this->options;

        try {
            $this->run();
        } catch (ValidationException $e) {
            http_response_code(400);
            echo $e->getMessage();
            exit;
        } catch (NotFoundException $e) {
            http_response_code(404);
            echo $e->getMessage();
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }

    }

    public function run()
    {
        $controller = new $this->options['model']($this->options);

        $result = NULL;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $controller->handlePost($_POST);
        } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $result = $controller->handleGet($_GET);
        } else {
            //404
            http_response_code(404);
            echo 'Not Found';
            exit;
        }

        $this->output = json_encode($result);
    }
}
