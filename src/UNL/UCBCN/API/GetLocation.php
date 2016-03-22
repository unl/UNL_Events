<?php
namespace UNL\UCBCN\API;

use UNL\UCBCN\Location;

class GetLocation {
	public $options = array();
	public $location;
	public $result;

	public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;

        $this->location = Location::getByID($this->options['location_id']);
        if ($this->location === FALSE) {
        	throw new \Exception("That location could not be found.", 404);
        }
    }

    public function handleGet($get)
    {
    	return $this->location;
    }

    public function handlePost($post)
    {
    	throw new NotFoundException('Not Found');
    }
}