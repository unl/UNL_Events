<?php
namespace UNL\UCBCN\API;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Location;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Manager\Controller as ManagerController;

use UNL\UCBCN\Manager\Auth;

class GetEvent
{
    public $options = array();
    public $calendar;
    public $event;
    public $on_main_calendar;

    public $result;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!in_array($this->calendar->id, $user->getCalendars()->getIDs())) {
            throw new \Exception("You do not have any permissions on this calendar. You must have a permission on this calendar to get or edit an event.", 403);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        if (empty($this->event->getStatusWithCalendar($this->calendar))) {
        	throw new \Exception("That event is not on this calendar.", 403);
        }

        $main_calendar = CalendarModel::getByID(ManagerController::$default_calendar_id);
        $this->on_main_calendar = $this->event->getStatusWithCalendar($main_calendar);
    }

    public function handleGet($get)
    {
        $this->event->imagedata = NULL;
    	return $this->event;
    }

    public function handlePost($post)
    {
    	if (!$this->event->userCanEdit()) {
            throw new \Exception("You do not have permission to edit this event.", 403);
        }
    	$this->updateEvent($post);
        $this->event->imagedata = NULL;
        return $this->event;
    }

    private function validateEventData($post_data) 
    {
        # title is required
        if (array_key_exists('title', $post_data) && empty($post_data['title'])) {
            throw new ValidationException('Title cannot be empty.');
        }
    }

    private function updateEvent($post_data) 
    {
        $this->validateEventData($post_data);

        # setting event data from post
        if (array_key_exists('title', $post_data)) {
            $this->event->title = $post_data['title'];
        }
        if (array_key_exists('subtitle', $post_data)) {
            $this->event->subtitle = $post_data['subtitle'];
        }
        if (array_key_exists('description', $post_data)) {
            $this->event->description = $post_data['description'];
        }
        if (array_key_exists('contact_name', $post_data)) {
            $this->event->listingcontactname = $post_data['contact_name'];
        }
        if (array_key_exists('contact_phone', $post_data)) {
            $this->event->listingcontactphone = $post_data['contact_phone'];
        }
        if (array_key_exists('contact_email', $post_data)) {
            $this->event->listingcontactemail = $post_data['contact_email'];
        }
        if (array_key_exists('website', $post_data)) {
            $this->event->webpageurl = $post_data['website'];
        }
        if (array_key_exists('private_public', $post_data)) {
            $this->event->approvedforcirculation = $post_data['private_public'] == 'private' ? 0 : 1;
        }

        $result = $this->event->update();

        # send to main calendar if selected and not already on main calendar
        # and box is checked
        if (!$this->on_main_calendar) {
            if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
                $this->event->considerForMainCalendar();
            }
        }

        return $result;
    }
}