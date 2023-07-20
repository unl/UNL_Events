<?php
namespace UNL\UCBCN\APIv2;

class Controller {
    public $options = array();
    public $request_data = array();
    public $output = array(
        'status' => 200,
        'message' => 'success',
        'data' => null,
    );
    public static $url = '/api/v2/';
    public $auth = null;
    public $user = false;

    public function __construct($options = array()) {
        $this->options = $options + $this->options;
        $this->auth = new Auth();

        // Handles all the errors and has default error messages
        try {
            $this->getRequestData();
            $this->run();
        } catch (ValidationException $e) {
            $this->output['status'] = 400;
            $this->output['message'] = $e->getMessage() !== "" ? $e->getMessage() : 'Bad Request';
        } catch (MissingAuthException $e) {
            $this->output['status'] = 401;
            $this->output['message'] = $e->getMessage() !== "" ? $e->getMessage() : 'Unauthorized';
        } catch (ForbiddenException $e) {
            $this->output['status'] = 403;
            $this->output['message'] = $e->getMessage() !== "" ? $e->getMessage() : 'Forbidden';
        } catch (NotFoundException $e) {
            $this->output['status'] = 404;
            $this->output['message'] = $e->getMessage() !== "" ? $e->getMessage() : 'Not Found';
        } catch (InvalidMethodException $e) {
            $this->output['status'] = 405;
            $this->output['message'] = $e->getMessage() !== "" ? $e->getMessage() : 'Method Not Allowed';
        } catch (ServerErrorException $e) {
            $this->output['status'] = 500;
            $this->output['message'] = $e->getMessage() !== "" ? $e->getMessage() : 'Internal Server Error';
        }catch (\Exception $e) {
            $this->output['status'] = 500;
            $this->output['message'] = $e->getMessage() !== "" ? $e->getMessage() : 'Internal Server Error';
        }
    }

    // This uses the model and auth to try running the API request
    public function run()
    {
        if (!isset($this->options['model'])) {
            throw new NotFoundException();
        }

        $model = new $this->options['model']($this->options);

        // Checks if that model requires Auth
        if ($model instanceof ModelAuthInterface && $model->needsAuth($_SERVER['REQUEST_METHOD'])) {
            $authCheck = false;

            // We check if we can use the cookie auth and they are validated
            if ($model->canUseCookieAuth($_SERVER['REQUEST_METHOD'])) {
                $authCheck = $this->checkAuthCookie();
            }

            // If they have not been validated yet then we check if we can use the token auth
            if (!$authCheck && $model->canUseTokenAuth($_SERVER['REQUEST_METHOD'])) {
                $authCheck = $this->checkAuthToken();
            }

            // If we are here then we know that they are not going to be authenticated
            if (!$authCheck) {
                throw new MissingAuthException();
            }
        }

        // We will try running the API query
        // User might be false if they are not authenticated
        $result = $model->run($_SERVER['REQUEST_METHOD'], $this->request_data, $this->user);

        $this->output['data'] = $result;
    }

    // Checks the headers of request for API token
    public function checkAuthToken(): bool
    {
        // Gets the headers
        $headers = getallheaders();
        if ($headers === false) {
            throw new ServerErrorException('Could not read request headers.');
        }

        // Checks for auth token
        if (!array_key_exists('Authorization', $headers)) {
            return false;
        }

        // Tries to authenticate with it
        $this->user = $this->auth->authenticateViaToken($headers['Authorization']);

        // returns bool
        return $this->user !== false;
    }

    // Checks the users cookies, this is like the normal auth through the website
    public function checkAuthCookie(): bool
    {
        // Checks auth
        $this->auth->checkAuthentication();
        if (!$this->auth->isAuthenticated()) {
            return false;
        }

        //sets the user and return true
        $this->user = $this->auth->getCurrentUser();
        return true;
    }

    // Cleans up the request data and validates the method
    public function getRequestData(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->request_data = $_POST;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->request_data = $_GET;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            parse_str(file_get_contents('php://input'), $_PUT);
            $this->request_data = $_PUT;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            parse_str(file_get_contents('php://input'), $_DELETE);
            $this->request_data = $_DELETE;
        } else {
            throw new InvalidMethodException('Method '. $_SERVER['REQUEST_METHOD']. ' is invalid.');
        }
    }
}
