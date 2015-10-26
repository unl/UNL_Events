<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Permissions;
use UNL\UCBCN\Manager\Controller;


class DeleteCalendarFinal extends PostHandler
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
        #  $this->options = $options + $this->options;
        $calendar = Calendar::getByShortname($this->options['calendar_shortname']);
        $user = Auth::getCurrentUser();

        if ($user->hasPermission(Permission::CALENDAR_DELETE_ID, $calendar->id)) {
             $calendar->deleteCalendar($calendar);
             $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Calendar Deleted','Your calendar has been deleted successfully');
             return '/manager/';
        }
        throw new \Exception("This user does Not have permission to delete this calendar", 404);
        return '/manager/';
       
    }

    
}