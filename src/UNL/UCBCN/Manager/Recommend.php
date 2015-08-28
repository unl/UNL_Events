<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendars;
use UNL\UCBCN\Manager\Auth;
use UNL\UCBCN\Event;
use UNL\UCBCN\Permission;

class Recommend extends PostHandler
{
    public $options = array();
    public $calendar;
    public $event;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_RECOMMEND_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to recommend events on this calendar.", 403);
        }

        $this->event = Event::getByID($this->options['event_id']);

        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->recommendEvent($post);
        return $this->calendar->getManageURL();
    }

    private function recommendEvent($post_data)
    {
        $pending_permission = Permission::getByName('Event Send Event to Pending Queue');
        $posted_permission = Permission::getByName('Event Post');
        $user = Auth::getCurrentUser();

        # add the event to the given calendars
        foreach ($post_data as $radio => $status) {
            $calendar_id = (int)(explode('calendar_', $radio)[1]);
            $calendar = Calendar::getByID($calendar_id);

            if ($status == 'pending') {
                if ($user->hasPermission($pending_permission->id, $calendar_id) || $calendar->recommendationswithinaccount) {
                    $calendar->addEvent($this->event, $status, $user, 'recommended');
                }
            } else if ($status == 'posted') {
                if ($user->hasPermission($posted_permission->id, $calendar_id)) {
                    $calendar->addEvent($this->event, $status, $user, 'recommended');
                }
            }

            
        }
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Event Recommended', 'The event ' . $this->event->title . ' has been recommended to other calendars.');
    }

    public function getRecommendableCalendars()
    {
        $user = Auth::getCurrentUser();

        $pending_permission = Permission::getByName('Event Send Event to Pending Queue');
        $posted_permission = Permission::getByName('Event Post');

        # get the calendars that are in this account with "recommendations within account"
        $calendars = new Calendars(array(
            'recommendable_within_account_id' => $this->calendar->account_id
        ));
        $calendars = $calendars->getIDs();

        # also get the calendars that the user has permissions to post to pending and post to posted
        $other_calendars = new Calendars(array(
            'recommend_permissions_for_user_uid' => $user->uid
        ));
        $other_calendars = $other_calendars->getIDs();

        # merge these two lists with details regarding calendar status
        $calendars = array_merge($calendars, $other_calendars);
        $calendar_properties = array();
        foreach($calendars as $cal_id) {
            # we need to know if the event is already on that calendar, and if not, what permissions the user has to push to it
            $calendar = Calendar::getByID($cal_id);

            if (($status = $this->event->getStatusWithCalendar($calendar)) != NULL) {
                $calendar_properties[$cal_id] = array(
                    'calendar' => $calendar,
                    'status' => $status,
                    'can_pending' => FALSE,
                    'can_posted' => FALSE
                );
            } else {
                # what are the permissions allowed
                $user_can_pending = $user->hasPermission($pending_permission->id, $cal_id) || $calendar->recommendationswithinaccount;
                $user_can_posted = $user->hasPermission($posted_permission->id, $cal_id);

                $calendar_properties[$cal_id] = array(
                    'calendar' => $calendar,
                    'status' => NULL,
                    'can_pending' => $user_can_pending,
                    'can_posted' => $user_can_posted
                );
            }
        }

        return $calendar_properties;
    }

}