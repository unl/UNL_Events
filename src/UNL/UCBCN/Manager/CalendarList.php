<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;

class CalendarList {

	public function __construct($options = array()) 
    {
        $calendars = $this->getCalendars();
        if (count($calendars) > 0) {
        	$calendars->seek(0);
        	$calendar = $calendars->current();
        	Controller::redirect($calendar->getManageURL());
        } else {
            # what the heck. This user doesn't have a calendar.
        }
    }

    public function getCalendars() {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }
}