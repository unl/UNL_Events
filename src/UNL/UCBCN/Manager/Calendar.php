<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\User;

class Calendar {
	public $options = array();

	public $calendar;

    public function __construct($options = array()) {
    	$this->options = $options + $this->options;
    	$this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);
    }

    public function getCategorizedEvents() {
    	$events = $this->calendar->getEvents();

    	$categories = array(
    		'pending' => array(),
    		'posted' => array(),
    		'archived' => array(),
    		'other' => array()
    	);

    	foreach ($events as $event) {
    		$status = $event->getStatusWithCalendar($this->calendar);
    		$key = array_key_exists($status, $categories) ? $status : 'other';

    		$categories[$key][] = $event;
    	}

    	return $categories;
    }

    public function getCalendars() {
    	$username = $_SESSION['__SIMPLECAS']['UID'];
    	$user = User::getByUid($username);

    	return $user->getCalendars();
    }
}