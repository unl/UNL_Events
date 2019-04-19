<?php
namespace UNL\UCBCN\API;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Location;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\Permission;

use UNL\UCBCN\Manager\Auth;

class CreateEvent
{
    public $options = array();
    public $calendar;
    public $event;

    public $result;

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

        $this->event = new Event;
    }

    public function handleGet($get) 
    {
        throw new NotFoundException('Not Found');
    }

    public function handlePost($post)
    {
        $new_event = $this->createEvent($post);
        return $new_event;
    }

    private function validateEventData($post_data) 
    {
        # title, start date, location are required
        if (empty($post_data['title']) || empty($post_data['location']) || empty($post_data['start_time']) || empty($post_data['end_time'])) {
            throw new ValidationException('Title, location, start time, and end time are required.');
        }

        # check that this location ID is legit
        if (Location::getByID($post_data['location']) === FALSE) {
            throw new ValidationException('That location ID is invalid.');
        }

        # timezone must be valid
        if (empty($post_data['timezone']) || !(in_array($post_data['timezone'], UNL\UCBCN::getTimezoneOptions()))) {
            throw new ValidationException('The timezone is invalid.');
        }

        # end date must be after start date
        $start_date = date('Y-m-d H:i:s', strtotime($post_data['start_time']));
        $end_date = date('Y-m-d H:i:s', strtotime($post_data['end_time']));
        if ($start_date > $end_date) {
            throw new ValidationException('Your end date/time must be on or after the start date/time.');
        }
    }

    private function createEvent($post_data) 
    {
        $user = Auth::getCurrentUser();
        
        # setting event data from post
        $this->event->title = empty($post_data['title']) ? NULL : $post_data['title'];
        $this->event->subtitle = empty($post_data['subtitle']) ? NULL : $post_data['subtitle'];
        $this->event->description = empty($post_data['description']) ? NULL : $post_data['description'];

        $this->event->listingcontactname = empty($post_data['contact_name']) ? NULL : $post_data['contact_name'];
        $this->event->listingcontactphone = empty($post_data['contact_phone']) ? NULL : $post_data['contact_phone'];
        $this->event->listingcontactemail = empty($post_data['contact_email']) ? NULL : $post_data['contact_email'];

        $this->event->webpageurl = empty($post_data['website']) ? NULL : $post_data['website'];
        $this->event->approvedforcirculation = array_key_exists('private_public', $post_data) && $post_data['private_public'] == 'private' ? 0 : 1;

        $this->validateEventData($post_data);

        $result = $this->event->insert($this->calendar, 'create event api');

        # add the event date time record
        $event_datetime = new Occurrence;
        $event_datetime->event_id = $this->event->id;
        $event_datetime->location_id = $post_data['location'];

        # set the start date and end date
        $event_datetime->timezone = empty($post_data['timezone']) ? UNL\UCBCN::$defaultTimezone : $post_data['timezone'];
        $event_datetime->starttime = date('Y-m-d H:i:s', strtotime($post_data['start_time']));
        $event_datetime->endtime = date('Y-m-d H:i:s', strtotime($post_data['end_time']));

        $event_datetime->recurringtype = 'none';
        $event_datetime->room = empty($post_data['room']) ? NULL : $post_data['room'];
        $event_datetime->directions = empty($post_data['directions']) ? NULL : $post_data['directions'];
        $event_datetime->additionalpublicinfo = empty($post_data['additional_public_info']) ? NULL : $post_data['additional_public_info'];

        $event_datetime->insert();

        if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'yes') {
            $this->event->considerForMainCalendar();
        }

        return $this->event;
    }
}