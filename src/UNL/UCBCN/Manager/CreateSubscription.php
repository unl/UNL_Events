<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Calendars;
use UNL\UCBCN\Calendar\Subscription;
use UNL\UCBCN\Calendar\SubscriptionHasCalendar;

class CreateSubscription extends PostHandler
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

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::CALENDAR_EDIT_SUBSCRIPTIONS_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit subscriptions on this calendar.", 403);
        }

        if (array_key_exists('subscription_id', $this->options)) {
            # we are editing an existing subscription
            $this->subscription = Subscription::getById($this->options['subscription_id']);

            if ($this->subscription == FALSE) {
                throw new \Exception("That subscription could not be found.", 404);
            }
        } else {
            $this->subscription = new Subscription;
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
      // defaults for creating subscription
      $subscriptionID = NULL;
      $errorURL = Controller::$url . $this->calendar->shortname . '/subscriptions/new/';
      $formType = 'Add';

      if (array_key_exists('subscription_id', $this->options)) {
        // editing subscription
        $subscriptionID = $this->options['subscription_id'];
        $errorURL = Controller::$url . $this->calendar->shortname . '/subscriptions/' . $subscriptionID . '/edit/';
        $formType = 'Edit';
      }

      // Validate subscription error and display error if invalid
      if (!$this->isValidSubscription($post, $error)) {
        $this->flashNotice(parent::NOTICE_LEVEL_ERROR, $formType . ' Subscription Error', $error);
        return $errorURL;
      }

      if (!empty($subscriptionID)) {
        # we are editing an existing subscription
        $this->subscription = Subscription::getById($subscriptionID);

        if ($this->subscription == FALSE) {
          throw new \Exception("That subscription could not be found.", 404);
        }

        $this->updateSubscription($post);
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Subscription Updated', 'Your subscription "' . $this->subscription->name . '" has been updated.');

      } else {
        # we are creating a new subscription
        $this->subscription = $this->createSubscription($post);
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Subscription Created', 'Your subscription "' . $this->subscription->name . '" has been created.');
      }

      //redirect
      return Controller::$url . $this->calendar->shortname . '/subscriptions/';
    }

    public function getAvailableCalendars() 
    {
        // Note: only include calendars with event activity in the last 6 months
        return new Calendars(array('has_event_activity_since' => date('Y-m-d', strtotime('-6 Months'))));
    }

    private function isValidSubscription($postData, &$error) {
        if (empty($postData['title'])) {
          $error = "A subscription must have a title.";
          return false;
        }

      if (empty($postData['calendars']) || !is_array($postData['calendars']) || count($postData['calendars']) < 1) {
        $error = "A subscription must have at least one calendar.";
        return false;
      }

      return true;
    }

    private function createSubscription($post_data) 
    {
        $subscription = new Subscription;
        $subscription->name = $post_data['title'];
        $trimmed = trim($subscription->name);
        if (empty($trimmed)) {
            $subscription->name = 'Subscription: ' . count($post_data['calendars']) . ' Calendar' . (count($post_data['calendars']) == 1 ? '' : 's');
        }
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

        # the subscription will go and get the events from those calendars that are relevant
        $subscription->process();

        return $subscription;
    }

    private function updateSubscription($post_data)
    {
        $this->subscription->name = $post_data['title'];
        $trimmed = trim($this->subscription->name);
        if (empty($trimmed)) {
            $this->subscription->name = 'Subscription: ' . count($post_data['calendars']) . ' Calendar' . (count($post_data['calendars']) == 1 ? '' : 's');
        }

        # see what calendars were removed from the subscription first...if they
        # are not present, remove the record from sub_has_calendar
        $current_subbed_calendars = $this->subscription->getSubscribedCalendars();
        $current_subbed_calendars_ids = array();
        foreach ($current_subbed_calendars as $cal) {
            $current_subbed_calendars_ids[] = $cal->id;
        }

        foreach ($current_subbed_calendars_ids as $calendar_id) {
            if (!in_array($calendar_id, $post_data['calendars'])) {
                # it has been deleted
                $record = SubscriptionHasCalendar::get($this->subscription->id, $calendar_id);
                $record->delete();
            }
        }

        # now add calendars that were not already in the subscription
        foreach ($post_data['calendars'] as $calendar_to_sub_id) {
            if (!in_array($calendar_to_sub_id, $current_subbed_calendars_ids)) {
                # add a new record
                $sub_has_calendar = new SubscriptionHasCalendar;
                $sub_has_calendar->calendar_id = $calendar_to_sub_id;
                $sub_has_calendar->subscription_id = $this->subscription->id;
                $sub_has_calendar->insert();
            }
        }
        
        # update the auto-aprove status
        $this->subscription->automaticapproval = $post_data['auto_approve'] == 'yes' ? 1 : 0;

        $this->subscription->save();

        # process the subscription again. Events that are currently already in there
        # from the subscription will not be added twice
        $this->subscription->process();

        return $this->subscription;
    }
}