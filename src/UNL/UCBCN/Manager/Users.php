<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;

class Users
{
    public $options = array();
    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 500);
        }
    }

    public function getUsers()
    {
        return $this->calendar->getUsers();
    }

}