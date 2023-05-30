<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN as BaseUCBCN;

class CalendarLocation extends PostHandler
{
    public $options = array();
    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;

        # check if we are looking to edit a calendar
        if (array_key_exists('calendar_shortname', $this->options)) {
            $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

            if ($this->calendar === FALSE) {
                throw new \Exception("That calendar could not be found.", 404);
            }

            # check permissions to edit this calendar's details
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::CALENDAR_EDIT_ID, $this->calendar->id)) {
                throw new \Exception("You do not have permission to edit the details of this calendar.", 403);
            }
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Calendar Location Updated', 'Your Calendar Location has been updated.');

        # redirect
        return $this->calendar->getManageURL();
    }
}