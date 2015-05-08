<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permissions;
use UNL\UCBCN\Manager\Controller;

class DeleteUser
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

        $this->user = User::getByUID($this->options['user_uid']);

        if ($this->user === FALSE) {
            throw new \Exception("That user could not be found.", 500);
        }

        $current_permissions = $this->user->getPermissions($this->calendar->id);
        foreach ($current_permissions as $permission) {
            $this->user->removePermission($permission->id, $this->calendar->id);
        }

        Controller::redirect($this->calendar->getUsersURL());
    }
}