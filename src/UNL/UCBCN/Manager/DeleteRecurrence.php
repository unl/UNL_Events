<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\Event\RecurringDate;

class DeleteRecurrence implements PostHandlerInterface
{
    public $options = array();
    public $calendar;
    public $event;
    public $event_datetime;
    public $recurrence;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        $this->event_datetime = Occurrence::getByID($this->options['event_datetime_id']);

        if ($this->event_datetime === FALSE) {
            throw new \Exception("That datetime could not be found.", 404);
        }

        if (!$this->event_datetime->isRecurring()) {
            throw new \Exception("That datetime is not recurring.", 400);
        }

        $this->recurrence = $this->event_datetime->getRecurrence($this->options['recurrence_id']);

        if ($this->recurrence === FALSE) {
            throw new \Exception("That recurrence does not exist.", 404);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        if (!isset($post['event_datetime_id'])) {
            throw new \Exception("The event_datetime_id must be set in the post data", 400);
        }

        if ($post['event_datetime_id'] != $this->event_datetime->id) {
            throw new \Exception("The event_datetime_id in the post data must match the event_datetime_id in the URL", 400);
        }

        if (!isset($post['recurrence_id'])) {
            throw new \Exception("The recurrence_id must be set in the post data", 400);
        }

        if ($post['recurrence_id'] != $this->recurrence->recurrence_id) {
            throw new \Exception("The recurrence_id in the post data must match the recurrence_id in the URL", 400);
        }

        $this->recurrence->unlink();

        // Redirect to the event edit page
        return $this->event->getEditURL($this->calendar);
    }
}