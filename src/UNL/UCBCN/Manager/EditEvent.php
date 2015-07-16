<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Location;
use UNL\UCBCN\Locations;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\User;

class EditEvent extends PostHandler
{
    public $options = array();
    public $calendar;
    public $event;
    public $on_main_calendar;
    public $page;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_EDIT_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit events on this calendar.", 403);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        if (!$this->event->userCanEdit()) {
            throw new \Exception("You do not have permission to edit this event.", 403);
        }

        if (array_key_exists('page', $_GET) && is_numeric($_GET['page']) && $_GET['page'] >= 1) {
            $this->page = $_GET['page'];
        } else {
            $this->page = 1;
        }

        $main_calendar = CalendarModel::getByID(Controller::$default_calendar_id);
        $this->on_main_calendar = $this->event->getStatusWithCalendar($main_calendar);
    }

    public function handlePost(array $get, array $post, array $files)
    {
        try {
            $this->updateEvent($_POST);
        } catch (ValidationException $e) {
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t edit your event', $e->getMessage());
            throw $e;
        }

        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Event Updated', 'The event "' . $this->event->title . '" has been updated.');
        return $this->event->getEditURL($this->calendar);
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

    private function setEventData($post_data)
    {
        $this->event->title = $post_data['title'];
        $this->event->subtitle = $post_data['subtitle'];
        $this->event->description = $post_data['description'];

        $this->event->listingcontactname = $post_data['contact_name'];
        $this->event->listingcontactphone = $post_data['contact_phone'];
        $this->event->listingcontactemail = $post_data['contact_email'];

        $this->event->webpageurl = $post_data['website'];
        $this->event->approvedforcirculation = $post_data['private_public'] == 'public' ? 1 : 0;
    }

    private function validateEventData($post_data)
    {
        # title required
        if (empty($post_data['title'])) {
            throw new ValidationException('<a href="#title">Title</a> is required.');
        }
    }

    private function updateEvent($post_data) 
    {
        $this->setEventData($post_data);
        $this->validateEventData($post_data);
        $result = $this->event->update();

        # update the event type record
        $event_has_type = EventType::getByEvent_ID($this->event->id);

        if ($event_has_type !== FALSE) {
            $event_has_type->eventtype_id = $post_data['type'];
            $event_has_type->update();
        } else {
            # create the type
            $event_has_type = new EventType;
            $event_has_type->event_id = $this->event->id;
            $event_has_type->eventtype_id = $post_data['type'];

            $event_has_type->insert();
        }

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