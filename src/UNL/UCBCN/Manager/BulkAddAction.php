<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;
use UNL\UCBCN\NotFoundException;
use UNL\UCBCN\Permission;
use UNL\UCBCN\UnexpectedValueException;
use UNL\UCBCN\User\PermissionException;

class BulkAddAction extends PostHandler
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
        // get the ids of the events to do things with
        $ids = explode(',', $post['ids']);
        $action = $post['action'];
        $source = isset($post['source']) ? $post['source'] : NULL;

        if ($action == 'move-to-upcoming') {
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::EVENT_MOVE_TO_UPCOMING_ID, $this->calendar->id)) {
                throw new PermissionException("You do not have permission to move events to upcoming on this calendar.", 403);
            }

            CalendarHasEvent::bulkAddEventToCalendar($this->calendar->id, $ids, Calendar::STATUS_POSTED, $source);
            $this->calendar->archiveEvents($ids);
            $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Events Added To Upcoming', count($ids) . ' events added with "upcoming" status. They will automatically move to "past" after the event.');
        } else if ($action == 'move-to-pending') {
            $user = Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::EVENT_MOVE_TO_PENDING_ID, $this->calendar->id)) {
                throw new PermissionException("You do not have permission to move events to pending on this calendar.", 403);
            }

            CalendarHasEvent::bulkAddEventToCalendar($this->calendar->id, $ids, Calendar::STATUS_PENDING, $source);
            $this->calendar->archiveEvents($ids);
            $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Events Added To Pending', count($ids) . ' events have been added to the "pending" tab.');
        } else {
            throw new UnexpectedValueException("Invalid bulk action.", 400);
        }

        //redirect
        return $this->calendar->getManageURL(TRUE);
    }
}