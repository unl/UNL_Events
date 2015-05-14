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
        }
    }

    public function getCalendars() {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }
}