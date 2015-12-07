<?php
namespace UNL\UCBCN\API;

use UNL\UCBCN\Calendar;

class Controller {
    public $options = array();
    public $calendar = NULL;
    public static $url = '/api/';
    
    public function __construct($options = array()) {
        $this->options = $options + $this->options;

        if (array_key_exists('calendar_shortname', $this->options)) {
            $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);
        }

        try {
            $this->run();
        } catch (ValidationException $e) {
            http_response_code(400);
            echo $e->message;
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo $e->message;
            exit;
        }

    }

    public function run()
    {
        $controller = new $this->options['model']($this->options);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handlePost($controller);
        }
    }

    protected function handlePost($object)
    {
        if (!($object instanceof PostHandler)) {
            throw new \Exception("The object is not an instance of the PostHandler", 500);
        }
        $result = $object->handlePost($_GET, $_POST, $_FILES);
        return $result;
    }
}
