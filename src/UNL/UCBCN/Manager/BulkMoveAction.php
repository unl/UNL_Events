<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;
use UNL\UCBCN\NotFoundException;
use UNL\UCBCN\Permission;
use UNL\UCBCN\UnexpectedValueException;
use UNL\UCBCN\User\PermissionException;

class BulkMoveAction extends PostHandler
{
    public $options = array();
    public $calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new NotFoundException("That calendar could not be found.", 404);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $backend_tab_name = $this->getBackendTabNameFromStatus($post['status']);
        if (empty($backend_tab_name)) {
            return $this->calendar->getManageURL();
        }

        // get the ids of the events to do things with
        $ids = explode(',', $post['ids']);

        switch ($post['action']) {
            case 'move-to-upcoming':
                $this->moveToUpcoming($ids, $backend_tab_name);
                break;
            case 'move-to-pending':
                $this->moveToPending($ids, $backend_tab_name);
                break;
            case 'delete':
                $this->deleteEvents($ids, $backend_tab_name);
                break;
            default:
                throw new UnexpectedValueException("Invalid bulk action.", 400);
        }

        //redirect
        return $this->calendar->getManageURL(TRUE);
    }

    private function getBackendTabNameFromStatus($status) {
        $map = array(
            'pending' => 'pending',
            'upcoming' => 'posted',
            'past' => 'archived'
        );

        return array_key_exists($status, $map) ? $map[$status] : NULL;
    }

    private function moveToUpcoming($ids, $backend_tab_name) {
        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_MOVE_TO_UPCOMING_ID, $this->calendar->id)) {
            throw new PermissionException("You do not have permission to move events to upcoming on this calendar.", 403);
        }

        CalendarHasEvent::bulkUpdateStatus($this->calendar->id, $ids, $backend_tab_name, Calendar::STATUS_POSTED);
        $this->calendar->archiveEvents($ids);
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Events Moved To Upcoming', count($ids) . ' events have been set to "upcoming" status. They will automatically move to "past" after the event.');
    }

    private function moveToPending($ids, $backend_tab_name) {
        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_MOVE_TO_PENDING_ID, $this->calendar->id)) {
            throw new PermissionException("You do not have permission to move events to pending on this calendar.", 403);
        }

        CalendarHasEvent::bulkUpdateStatus($this->calendar->id, $ids, $backend_tab_name, Calendar::STATUS_PENDING);
        $this->calendar->archiveEvents($ids);
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Events Moved To Pending', count($ids) . ' events have been moved to the "pending" tab.');
    }

    private function deleteEvents($ids, $backend_tab_name) {
        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_DELETE_ID, $this->calendar->id)) {
            throw new PermissionException("You do not have permission to delete events on this calendar.", 403);
        }

        foreach ($ids as $id) {
            $calendar_has_event = CalendarHasEvent::getByIdsStatus($this->calendar->id, $id, $backend_tab_name);

            if ($calendar_has_event !== FALSE) {
                $calendar_has_event->delete();
            }
        }
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Events Deleted', count($ids) . ' events have been deleted.');
    }
}
