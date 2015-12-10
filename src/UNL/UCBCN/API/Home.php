<?php
namespace UNL\UCBCN\API;

class Home
{
    public $options = array();

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
    }

    public function handleGet($get) {
        $result = new \stdClass;
        $result->welcome = 'UNL Events API';
        return $result;
    }

    public function handlePost($post) {
        throw new NotFoundException('Not Found');
    }
}