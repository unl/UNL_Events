<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Subscription;
use UNL\UCBCN\Calendar\SubscriptionHasCalendar;

class DeleteSubscription
{
    public $options = array();
    public $calendar;
    public $subscription;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 500);
        }

        $this->subscription = Subscription::getByID($this->options['subscription_id']);

        if ($this->subscription === FALSE) {
            throw new \Exception("That subscription could not be found.", 500);
        }

        foreach($this->subscription->getSubscribedCalendars() as $calendar) {
            $record = SubscriptionHasCalendar::get($this->subscription->id, $calendar->id);
            $record->delete();
        }

        $this->subscription->delete();
        
        Controller::redirect($this->calendar->getSubscriptionsURL());
    }
}