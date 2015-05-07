<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permissions;
use UNL\UCBCN\Manager\Controller;

class AddUser
{
    public $options = array();
    public $calendar;
    public $user;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 500);
        }

        # check if we are posting to this controller
        if (!empty($_POST)) {
            # we are adding a new user
            $this->user = $this->addUser($_POST);

            Controller::redirect($this->calendar->getUsersURL());
        }

        # check if we are looking to edit a calendar
        if (array_key_exists('calendar_shortname', $this->options)) {
            $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

            if ($this->calendar === FALSE) {
                throw new \Exception("That calendar could not be found.", 500);
            }
        } else {
            # we are creating a new calendar
            $this->calendar = new Calendar;
        }
    }

    public function getAvailableUsers()
    {
        return $this->calendar->getUsersNotOnCalendar();
    }

    public function getAllPermissions()
    {
        return new Permissions;
    }

    private function addUser($post_data)
    {
        $user = User::getByUID($post_data['user']);

        foreach ($post_data as $key => $value) {
            if (strpos($key, 'permission_') === 0 && $value == 'on') {
                # this permission is checked
                $perm_id = (int)(substr($key, 11));
                $user->grantPermission($perm_id, $this->calendar->id);
            }
        }

        return $user;
    }

}