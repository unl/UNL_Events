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
    public $post;
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
            $this->updateEvent($post, $files);
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

    private function setEventData($post_data, $files)
    {
        $this->event->title = empty($post_data['title']) ? NULL : $post_data['title'];
        $this->event->subtitle = empty($post_data['subtitle']) ? NULL : $post_data['subtitle'];
        $this->event->description = empty($post_data['description']) ? NULL : $post_data['description'];

        $this->event->listingcontactname = empty($post_data['contact_name']) ? NULL : $post_data['contact_name'];
        $this->event->listingcontactphone = empty($post_data['contact_phone']) ? NULL : $post_data['contact_phone'];
        $this->event->listingcontactemail = empty($post_data['contact_email']) ? NULL : $post_data['contact_email'];

        $this->event->webpageurl = empty($post_data['website']) ? NULL : $post_data['website'];
        $this->event->approvedforcirculation = $post_data['private_public'] == 'private' ? 0 : 1;

        # for extraneous data aside from the event (location, type, etc)
        $this->post = $post_data;
    }

    private function validateEventData($post_data, $files)
    {
        # title required
        if (empty($post_data['title'])) {
            throw new ValidationException('<a href="#title">Title</a> is required.');
        }

        # if we are sending this to UNL Main Calendar, description and contact info must be given
        if (!$this->on_main_calendar) {
            if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
                if (empty($post_data['description']) || empty($post_data['contact_name'])) {
                    throw new ValidationException('<a href="#contact-name">Contact name</a> and <a href="#description">description</a> are required to recommend to UNL Main Calendar.');
                }
            }
        }

        if (array_key_exists('remove_image', $post_data) && $post_data['remove_image'] == 'on') {
            $this->event->imagemime = NULL;
            $this->event->imagedata = NULL;
        } else if (isset($files['imagedata']) && is_uploaded_file($files['imagedata']['tmp_name'])) {
            if ($files['imagedata']['error'] == UPLOAD_ERR_OK) {
                $this->event->imagemime = $files['imagedata']['type'];
                $this->event->imagedata = file_get_contents($files['imagedata']['tmp_name']);
            } else {
                throw new ValidationException('There was an error uploading your image.');
            }
        } else if (isset($files['imagedata']) && $files['imagedata']['error'] == UPLOAD_ERR_INI_SIZE) {
            throw new ValidationException('Your image file size was too large. It must be 2 MB or less. Try a tool like <a target="_blank" href="http://www.imageoptimizer.net">Image Optimizer</a>.');
        }
    }

    private function updateEvent($post_data, $files) 
    {
        $this->setEventData($post_data, $files);
        $this->validateEventData($post_data, $files);
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