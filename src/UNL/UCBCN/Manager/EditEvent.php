<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Manager\EventForm as EventForm;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Audience;
use UNL\UCBCN\Calendar as CalendarModel;

class EditEvent extends EventForm
{
    public $on_main_calendar;
    public $page;

    public function __construct($options = array()) 
    {
        parent::__construct($options);

	    $this->mode = self::MODE_UPDATE;
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
                    throw new ValidationException('<a href="#contact-name">Contact name</a>, <a href="#description">description</a> and <a href="#imagedata">image</a> are required to recommend to UNL Main Calendar.');
                }
            }
        }

        # website must be a valid url
        if (!empty($post_data['website']) && !filter_var($post_data['website'], FILTER_VALIDATE_URL)) {
            throw new ValidationException('Event Website must be a valid URL.');
        }

        if (!empty($post_data['contact_type']) && $post_data['contact_type'] !== "person" && $post_data['contact_type'] !== "organization") {
            throw new ValidationException('<a href="#contact-type">Contact Type</a> must be person or organization.');
        }

        # website must be a valid url
        if (!empty($post_data['contact_website']) && !filter_var($post_data['contact_website'], FILTER_VALIDATE_URL)) {
            throw new ValidationException('Contact Website must be a valid URL.');
        }

	    // Validate Image
	    $this->validateEventImage($post_data, $files);
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

        // Update Audience Records
        $all_audiences = $this->getAudiences();

        foreach ($all_audiences as $current_audience) {
            $target_audience_id = 'target-audience-' . $current_audience->id;
            
            // get the audiences currently associated with the event
            // (these will change since we are adding and deleting)
            // we then check and store the audience that matches the one we are looking for
            $event_audiences = $this->event->getAudiences();
            $target_audience_record = false;
            foreach ($event_audiences as $target_audience) {
                if ($current_audience->id === $target_audience->audience_id) {
                    $target_audience_record = $target_audience;
                    break;
                }
            }

            // if the audience has been checked but the event does not have it yet
            if (isset($post_data[$target_audience_id]) &&
                $post_data[$target_audience_id] === $current_audience->id &&
                $target_audience_record === false) {

                $event_targets_audience = new Audience;
                $event_targets_audience->event_id = $this->event->id;
                $event_targets_audience->audience_id = $post_data[$target_audience_id];

                $event_targets_audience->insert();

            // if the audience has not been checked and the audience has it
            } elseif (!isset($post_data[$target_audience_id]) &&
                $target_audience_record !== false) {

                $target_audience_record->delete();
            }
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
