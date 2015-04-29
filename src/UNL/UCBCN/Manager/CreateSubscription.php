<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendars;
use UNL\UCBCN\Calendar\Subscription;
use UNL\UCBCN\Calendar\SubscriptionHasCalendar;

class CreateSubscription
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

        # check if we are posting to this controller
        if (!empty($_POST)) {
            if (array_key_exists('subscription_id', $this->options)) {
                # we are editing an existing subscription
                $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

                if ($this->calendar === FALSE) {
                    throw new \Exception("That calendar could not be found.", 500);
                }

                $this->updateCalendar($_POST);
            } else {
                # we are creating a new subscription
                $this->subscription = $this->createSubscription($_POST);
            }

            header('Location: /manager/' . $this->calendar->shortname . '/subscriptions/');
        }

        $this->subscription = new Subscription;
    }

    public function getAvailableCalendars() 
    {
        return new Calendars;
    }

    private function createSubscription($post_data) 
    {
        error_log(print_r($post_data, 1));

        $subscription = new Subscription;
        $subscription->name = $post_data['title'];
        $subscription->automaticapproval = $post_data['auto_approve'] == 'yes' ? 1 : 0;
        $subscription->calendar_id = $this->calendar->id;

        $subscription->insert();
        
        # add subscription_has_calendars for each one selected
        foreach($post_data['calendars'] as $calendar_id) {
            $sub_has_calendar = new SubscriptionHasCalendar;
            $sub_has_calendar->calendar_id = $calendar_id;
            $sub_has_calendar->subscription_id = $subscription->id;
            $sub_has_calendar->insert();
        }

        $subscription->process();

        return $subscription;
    }

}