<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Permissions;
use UNL\UCBCN\Manager\Controller;

class CleanupCalendar extends PostHandler
{       
    public $options = array();
    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_DELETE_ID, $this->calendar->id)){
            throw new \Exception("You do not have permission to delete events on this calendar.", 400);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $count = $this->calendar->purgePastEventsByStatus(Calendar::STATUS_ARCHIVED, $post['past_duration']);

        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Events Deleted', $count . ' events have been removed from your calendar.');

        //redirect
        return $this->calendar->getCleanupURL();
    }
}
