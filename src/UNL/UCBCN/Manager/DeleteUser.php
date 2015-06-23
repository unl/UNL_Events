<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permissions;
use UNL\UCBCN\Manager\Controller;

class DeleteUser implements PostHandlerInterface
{
    public $options = array();
    public $calendar;
    public $user;

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

        $this->user = User::getByUID($this->options['user_uid']);

        if ($this->user === FALSE) {
            throw new \Exception("That user could not be found.", 404);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        if (!isset($post['user_uid'])) {
            throw new \Exception("The user_uid must be set in the post data", 400);
        }

        if ($post['user_uid'] != $this->user->uid) {
            throw new \Exception("The user_uid in the post data must match the user_uid in the URL", 400);
        }

        $current_permissions = $this->user->getPermissions($this->calendar->id);
        foreach ($current_permissions as $permission) {
            $this->user->removePermission($permission->id, $this->calendar->id);
        }

        //Redirect to the user list
        return $this->calendar->getUsersURL();
    }
}