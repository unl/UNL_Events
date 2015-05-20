<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Location;
use UNL\UCBCN\Locations;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\User;

class CreateEvent implements PostHandlerInterface
{
    public $options = array();

    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }
    }

    public function getEventTypes()
    {
        return new EventTypes(array());
    }

    public function getLocations()
    {
        $user = Auth::getCurrentUser();
        return new Locations(array('user_id' => $user->uid));
    }

    private function calculateDate($date, $hour, $minute, $am_or_pm)
    {
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
        $user = Auth::getCurrentUser();

        $event = new Event();
        $event->title = $post_data['title'];
        $event->subtitle = $post_data['subtitle'];
        $event->description = $post_data['description'];

        $event->listingcontactname = $post_data['contact_name'];
        $event->listingcontactphone = $post_data['contact_phone'];
        $event->listingcontactemail = $post_data['contact_email'];

        $event->webpageurl = $post_data['website'];
        $event->approvedforcirculation = $post_data['private_public'] == 'public' ? 1 : 0;

        $add_to_default = array_key_exists('send_to_main', $post_data) && 
            $post_data['send_to_main'] == 'on';
        $result = $event->insert($this->calendar, 'create event form');

        # add the event type record
        $event_has_type = new EventType;
        $event_has_type->event_id = $event->id;
        $event_has_type->eventtype_id = $post_data['type'];

        $event_has_type->insert();

        # add the event date time record
        $event_datetime = new Occurrence;
        $event_datetime->event_id = $event->id;

        # check if this is to use a new location
        if ($post_data['location'] == 'new') {
            # create a new location
            $location = new Location;
            $location->name = $post_data['location_name'];
            $location->streetaddress1 = $post_data['location_address_1'];
            $location->streetaddress2 = $post_data['location_address_2'];
            $location->room = $post_data['location_room'];
            $location->city = $post_data['location_city'];
            $location->state = $post_data['location_state'];
            $location->zip = $post_data['location_zip'];
            $location->mapurl = $post_data['location_map_url'];
            $location->webpageurl = $post_data['location_webpage'];
            $location->hours = $post_data['location_hours'];
            $location->directions = $post_data['location_directions'];
            $location->additionalpublicinfo = $post_data['location_additional_public_info'];
            $location->type = $post_data['location_type'];
            $location->phone = $post_data['location_phone'];
            if (array_key_exists('location_save', $post_data) && $post_data['location_save'] == 'on') {
                $location->user_id = $user->uid;
            }
            $location->standard = 0;

            $location->insert();
            $event_datetime->location_id = $location->id;
        } else {
            $event_datetime->location_id = $post_data['location'];    
        }

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

        if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
            $event->considerForMainCalendar();
        }

        return $event;
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->saveEvent($post);
        
        //redirect
        return '/manager/' . $this->calendar->shortname . '/';
    }
}