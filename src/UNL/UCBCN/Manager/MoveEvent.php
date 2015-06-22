<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;

class MoveEvent implements PostHandlerInterface
{
    public $options = array();
    public $calendar;
    public $event;

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
    }

    public function handlePost(array $get, array $post, array $files)
    {
        if (!isset($post['event_id'])) {
            throw new \Exception("The event_id must be set in the post data.", 400);
        }

        if ($post['event_id'] != $this->event->id) {
            throw new \Exception("The event_id in the post data must match the event_id in the URL.", 400);
        }

        $calendar_has_event = CalendarHasEvent::getByIDs($this->calendar->id, $this->event->id);

        if ($calendar_has_event == FALSE) {
            throw new \Exception("This calendar does not have that event.", 400);
        }

        if ($post['new_status'] == 'pending') {
            $calendar_has_event->status = CalendarHasEvent::STATUS_PENDING;
        } else if  ($post['new_status'] == 'upcoming') {
            $calendar_has_event->status = CalendarHasEvent::STATUS_POSTED;
        } else {
            throw new \Exception("Invalid status for event.", 400);
        }
        
        $calendar_has_event->save();

        //redirect
        return $this->calendar->getManageURL();
    }
}