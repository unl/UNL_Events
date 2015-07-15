<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Subscriptions as SubscriptionList;
use UNL\UCBCN\Permission;

class Subscription {
	public $options = array();
	public $calendar;

	public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 500);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::CALENDAR_EDIT_SUBSCRIPTIONS_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit subscriptions on this calendar.", 403);
        }
    }

    public function getSubscriptions()
    {
    	$options = array(
    		'calendar_id' => $this->calendar->id
    	);
    	$subscriptions = new SubscriptionList($options);

    	return $subscriptions;
    }
}