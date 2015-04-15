<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Locations;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\User;

class CreateEvent 
{
	public $options = array();

    public $calendar;

    public function __construct($options = array()) 
    {
    	$this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if (!empty($_POST)) {
            error_log(print_r($_POST,1));
            $this->saveEvent($_POST);
            header('Location: /manager/' . $this->calendar->shortname . '/');
        }

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 500);
        }
    }

    public function getEventTypes()
    {
        return new EventTypes(array());
    }

    public function getLocations()
    {
        $username = $_SESSION['__SIMPLECAS']['UID'];
        return new Locations(array('user_id' => $username));
    }

    private function calculateDate($date, $hour, $minute, $am_or_pm) {
        $date = strtotime($date);
        # add hours correctly
        # TODO: handle when this is not entered
        $hours_to_add = (int)($hour) % 12;
        if ($am_or_pm == 'pm') {
            $hours_to_add += 12;
        }
        $date += $hours_to_add * 60 * 60 + (int)($minute) * 60;
        return date('Y-m-d H:i:s', $date);
    }

    private function saveEvent($post_data) 
    {
        $username = $_SESSION['__SIMPLECAS']['UID'];
        $user = User::getByUid($username);

        $event = new Event();
        $event->title = $post_data['title'];
        $event->subtitle = $post_data['subtitle'];
        $event->description = $post_data['description'];

        $event->listingcontactname = $post_data['contact_name'];
        $event->listingcontactphone = $post_data['contact_phone'];
        $event->listingcontactemail = $post_data['contact_email'];

        $event->webpageurl = $post_data['website'];

        $add_to_default = array_key_exists('send_to_main', $post_data) && 
            $post_data['send_to_main'] == 'on';
        $event->insert();

        # add the event to the calendar we are saving it on
        $this->calendar->addEvent($event, 'pending', $user, 'create event form');

        # add the event type record
        $event_has_type = new EventType;
        $event_has_type->event_id = $event->id;
        $event_has_type->eventtype_id = $post_data['type'];

        $event_has_type->insert();

        # add the event date time record
        $event_datetime = new Occurrence;
        $event_datetime->event_id = $event->id;
        $event_datetime->location_id = $post_data['location'];

        # set the start date and end date
        $event_datetime->starttime = $this->calculateDate($post_data['start_date'], 
            $post_data['start_time_hour'], $post_data['start_time_minute'], 
            $post_data['start_time_am_pm']);

        $event_datetime->endtime = $this->calculateDate($post_data['end_date'], 
            $post_data['end_time_hour'], $post_data['end_time_minute'], 
            $post_data['end_time_am_pm']);

        if (array_key_exists('recurring', $post_data) && $post_data['recurring'] != 'on') {
            $event_datetime->recurringtype = 'none';
        } else {
            $event_datetime->recurringtype = $post_data['recurring_type'];
            $event_datetime->recurs_until = $this->calculateDate(
                $post_data['recurs_until_date'], $post_data['recurs_until_time_hour'], 
                $post_data['recurs_until_time_minute'], 
                $post_data['recurs_until_time_am_pm']);
            if ($event_datetime->recurringtype == 'monthly') {
                $event_datetime->rectypemonth = $post_data['recurring_monthly_type'];
            }

        }
        $event_datetime->room = $post_data['room'];
        $event_datetime->directions = $post_data['directions'];
        $event_datetime->additionalpublicinfo = $post_data['additional_public_info'];

        $event_datetime->insert();

    }
}