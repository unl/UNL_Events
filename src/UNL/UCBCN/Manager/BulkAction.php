<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;
use UNL\UCBCN\Permission;

class BulkAction implements PostHandlerInterface
{
    public $options = array();
    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        // get the ids of the events to do things with
        $ids = explode(',', $post['ids']);
        $action = $post['action'];

        if ($action == 'move-to-upcoming') {
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::EVENT_MOVE_TO_UPCOMING_ID, $this->calendar->id)) {
                throw new \Exception("You do not have permission to move events to upcoming on this calendar.", 403);
            }

            foreach ($ids as $id) {
                $calendar_has_event = CalendarHasEvent::getByIDs($this->calendar->id, $id);

                if ($calendar_has_event !== FALSE) {
                    $calendar_has_event->status = Calendar::STATUS_POSTED;
                    $calendar_has_event->save();
                }
            }
        } else if ($action == 'move-to-pending') {
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::EVENT_MOVE_TO_PENDING_ID, $this->calendar->id)) {
                throw new \Exception("You do not have permission to move events to pending on this calendar.", 403);
            }
            
            foreach ($ids as $id) {
                $calendar_has_event = CalendarHasEvent::getByIDs($this->calendar->id, $id);

                if ($calendar_has_event !== FALSE) {
                    $calendar_has_event->status = Calendar::STATUS_PENDING;
                    $calendar_has_event->save();
                }
            }
        } else if ($action == 'delete') {
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::EVENT_DELETE_ID, $this->calendar->id)) {
                throw new \Exception("You do not have permission to delete events on this calendar.", 403);
            }

            foreach ($ids as $id) {
                $calendar_has_event = CalendarHasEvent::getByIDs($this->calendar->id, $id);

                if ($calendar_has_event !== FALSE) {
                    $calendar_has_event->delete();
                }
            }
        } else {
            throw new \Exception("Invalid bulk action.", 400);
        }

        //redirect
        return $this->calendar->getManageURL();
    }
}