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
use UNL\UCBCN\Permission;

class CreateEvent extends PostHandler
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

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_CREATE_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to create an event on this calendar.", 403);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $new_event = $this->saveEvent($post);
        $this->flashNotice(NOTICE_LEVEL_SUCCESS, 'Event Created', 'Your event "' . $new_event->title . '"" has been created.');

        # redirect
        return '/manager/' . $this->calendar->shortname . '/';
    }

    public function getEventTypes()
    {
        return new EventTypes(array());
    }
    
    public function getUserLocations()
    {
        $user = Auth::getCurrentUser();
        return new Locations(array('user_id' => $user->uid));
    }
    
    public function getStandardLocations($display_order)
    {
        return new Locations(array(
            'standard' => true,
            'display_order' => $display_order,
        ));
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
            $location = $this->addLocation($post_data, $user);
            
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

        if (array_key_exists('recurring', $post_data) && $post_data['recurring'] == 'on') {
            $event_datetime->recurringtype = $post_data['recurring_type'];
            $event_datetime->recurs_until = $this->calculateDate(
                $post_data['recurs_until_date'], 11, 59, 'PM');
            if ($event_datetime->recurringtype == 'date' || 
                $event_datetime->recurringtype == 'lastday' || 
                $event_datetime->recurringtype == 'first' ||
                $event_datetime->recurringtype == 'second' ||
                $event_datetime->recurringtype == 'third'|| 
                $event_datetime->recurringtype == 'fourth' ||
                $event_datetime->recurringtype == 'last') {
                    $event_datetime->rectypemonth = $event_datetime->recurringtype;
                    $event_datetime->recurringtype = 'monthly';
            }
        } else {
            $event_datetime->recurringtype = 'none';
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

    /**
     * Add a location
     * 
     * @param array $post_data
     * @return Location
     */
    protected function addLocation(array $post_data, $user)
    {
        $allowed_fields = array(
            'name',
            'streetaddress1',
            'streetaddress2',
            'room',
            'city',
            'state',
            'zip',
            'mapurl',
            'webpageurl',
            'hours',
            'directions',
            'additionalpublicinfo',
            'type',
            'phone',
        );

        $location = new Location;

        foreach ($allowed_fields as $field) {
            $location->$field = $post_data['new_location'][$field];
        }

        if (array_key_exists('location_save', $post_data) && $post_data['location_save'] == 'on') {
            $location->user_id = $user->uid;
        }
        $location->standard = 0;

        $location->insert();
        
        return $location;
    }
}