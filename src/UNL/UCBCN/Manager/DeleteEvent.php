<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;
use UNL\UCBCN\Permission;

class DeleteEvent extends PostHandler
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

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_DELETE_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to delete events on this calendar.", 403);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        # get the Calendar Has Event record
        $calendar_has_event = CalendarHasEvent::getByIDs($this->calendar->id, $this->event->id);

        # check if this is where the event was originally created
        if ($calendar_has_event->source == 'create event form') {
            # delete the event from the entire system
            $this->event->delete();
            $this->flashNotice(NOTICE_LEVEL_SUCCESS, 'Event Deleted', 'The event ' . $this->event->title . ' has been removed from the system.');
        } else {
            # delete the calendar has event record
            $calendar_has_event->delete();
            $this->flashNotice(NOTICE_LEVEL_SUCCESS, 'Event Deleted', 'The event ' . $this->event->title . ' has been removed from your calendar.');
        }

        //redirect
        return $this->calendar->getManageURL();
    }
}