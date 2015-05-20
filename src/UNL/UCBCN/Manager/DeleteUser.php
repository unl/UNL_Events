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

        if (empty($_POST)) {
            throw new \Exception("Deletion requires a POST request", 400);
        }

        $this->deleteUser($_POST);

        Controller::redirect($this->calendar->getUsersURL());
    }
    
    protected function deleteUser($post_data)
    {
        if (!isset($post_data['user_uid'])) {
            throw new \Exception("The user_uid must be set in the post data", 400);
        }

        if ($post_data['user_uid'] != $this->user->uid) {
            throw new \Exception("The user_uid in the post data must match the user_uid in the URL", 400);
        }

        $current_permissions = $this->user->getPermissions($this->calendar->id);
        foreach ($current_permissions as $permission) {
            $this->user->removePermission($permission->id, $this->calendar->id);
        }
    }
}