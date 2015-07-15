<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;

class Users
{
    public $options = array();
    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }
        
        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::CALENDAR_EDIT_PERMISSIONS_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit user permissions on this calendar.", 403);
        }
    }

    public function getUsers()
    {
        return $this->calendar->getUsers();
    }

}