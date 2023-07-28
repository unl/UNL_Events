<?php
namespace UNL\UCBCN\API;

class CreateLocation {
    public $options = array();
    public $location;
    public $result;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    public function handleGet()
    {
        http_response_code('503');
        echo 'This API endpoint has been shutdown, please use the new API.';
        exit;
    }

    public function handlePost()
    {
        http_response_code('503');
        echo 'This API endpoint has been shutdown, please use the new API.';
        exit;
    }
}