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
            throw new \Exception("That calendar could not be found.", 404);
        }

        $this->subscription = Subscription::getByID($this->options['subscription_id']);

        if ($this->subscription === FALSE) {
            throw new \Exception("That subscription could not be found.", 404);
        }
        
        if (empty($_POST)) {
            throw new \Exception("Deletion requires a POST request", 400);
        }
        
        $this->deleteSubscription($_POST);
        
        Controller::redirect($this->calendar->getSubscriptionsURL());
    }

    /**
     * @param array $post_data - the post data to handle
     * @throws \Exception
     */
    protected function deleteSubscription(array $post_data)
    {
        if (!isset($post_data['subscription_id'])) {
            throw new \Exception("The subscription_id must be set in the post data", 400);
        }
        
        if ($post_data['subscription_id'] != $this->subscription->id) {
            throw new \Exception("The subscription_id in the post data must match the subscriptions_id in the URL", 400);
        }
        
        foreach($this->subscription->getSubscribedCalendars() as $calendar) {
            $record = SubscriptionHasCalendar::get($this->subscription->id, $calendar->id);
            $record->delete();
        }

        $this->subscription->delete();
    }
}