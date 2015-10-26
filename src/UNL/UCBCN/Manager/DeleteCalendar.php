<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Permissions;
use UNL\UCBCN\Manager\Controller;
use UNL\UCBCN\Calendar as CalendarModel;

class DeleteCalendar extends PostHandler
{       

    public $options = array();
    public $calendar;


    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }
        
    }

    public function handlePost(array $get, array $post, array $files)
    {
        
    }

    
}