<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;
use UNL\UCBCN\Permission;

class MoveEvent extends PostHandler
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
            throw new \Exception("This calendar does not have that event.", 404);
        }

        if ($post['new_status'] == 'pending') {
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::EVENT_MOVE_TO_PENDING_ID, $this->calendar->id)) {
                throw new \Exception("You do not have permission to move events to pending on this calendar.", 403);
            }
            $calendar_has_event->status = CalendarHasEvent::STATUS_PENDING;

            $calendar_has_event->save();
            $this->flashNotice(NOTICE_LEVEL_SUCCESS, 'Event Moved To Pending', $this->event->title . ' has been set to "pending" status.');
        } else if ($post['new_status'] == 'upcoming') {
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::EVENT_MOVE_TO_UPCOMING_ID, $this->calendar->id)) {
                throw new \Exception("You do not have permission to move events to upcoming on this calendar.", 403);
            }
            $calendar_has_event->status = CalendarHasEvent::STATUS_POSTED;

            $calendar_has_event->save();
            $this->flashNotice(NOTICE_LEVEL_SUCCESS, 'Event Moved To Upcoming', $this->event->title . ' has been set to "upcoming" status. It will automatically move to "past" after the event.');
        } else {
            throw new \Exception("Invalid status for event.", 400);
        }
        
        //redirect
        return $this->calendar->getManageURL();
    }
}