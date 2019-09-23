<?php
namespace UNL\UCBCN;

use UNL\UCBCN\Manager\Auth;
use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Event as Event;
use UNL\UCBCN\Events as Events;
use UNL\UCBCN\Frontend\Controller as FrontendController;
use UNL\UCBCN\Manager\Controller as ManagerController;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Calendar\Events as CalendarHasEvents;
use UNL\UCBCN\Calendar\Subscriptions;
use UNL\UCBCN\Users;

/**
 * Details related to a calendar within the UNL Event Publisher system.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

/**
 * ORM for a record within the database.
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Calendar extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $account_id;                      // int(10)  not_null multiple_key unsigned
    public $name;                            // string(255)
    public $shortname;                       // string(100)  multiple_key
    public $defaulttimezone;                 // string(30)
    public $website;                         // string(255)
    public $eventreleasepreference;          // string(255)
    public $calendardaterange;               // int(10)  unsigned
    public $formatcalendardata;              // blob(4294967295)  blob
    public $uploadedcss;                     // blob(4294967295)  blob
    public $uploadedxsl;                     // blob(4294967295)  blob
    public $emaillists;                      // blob(4294967295)  blob
    public $calendarstatus;                  // string(255)
    public $datecreated;                     // datetime(19)  binary
    public $uidcreated;                      // string(255)
    public $datelastupdated;                 // datetime(19)  binary
    public $uidlastupdated;                  // string(255)
    public $externalforms;                   // string(255)
    public $recommendationswithinaccount;    // int(1)
    public $theme;                           // string(255)

    const STATUS_PENDING  = 'pending';
    const STATUS_POSTED   = 'posted';
    const STATUS_ARCHIVED = 'archived';

    const CLEANUP_YEARS_1 = '-1 Year';
    const CLEANUP_YEARS_2 = '-2 Years';
    const CLEANUP_YEARS_3 = '-3 Years';
    const CLEANUP_YEARS_4 = '-4 Years';
    const CLEANUP_YEARS_5 = '-5 Years';
    const CLEANUP_YEARS_10 = '-10 Years';
    const CLEANUP_MONTH_1 = '-1 Month';

    const EVENT_RELEASE_PREFERENCE_DEFAULT   = null;
    const EVENT_RELEASE_PREFERENCE_IMMEDIATE = 1;
    const EVENT_RELEASE_PREFERENCE_PENDING   = 0;
    
    public static function getTable()
    {
        return 'calendar';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
    public function getFrontendURL() {
        return FrontendController::$url . $this->shortname . "/";
    }

    public function getManageURL($append_page_tab = FALSE) {
        $append = '';
        if ($append_page_tab) {
            $append = array();
            if (array_key_exists('current_tab', $_SESSION)) {
                $append['tab'] = $_SESSION['current_tab'];
            }
            if (array_key_exists('current_page', $_SESSION)) {
                $append['page'] = $_SESSION['current_page'];
            }
            if (!empty($append)) {
                $append = '?' . join(array_map(function ($key, $val) {return $key . '=' . $val;}, array_keys($append), $append), '&');
            } else {
                $append = '';
            }
        }
        return ManagerController::$url . $this->shortname . "/" . $append;
    }

    public function getNewURL() {
        return ManagerController::$url . 'calendar/new/';
    }

    public function getEditURL() {
        return ManagerController::$url . $this->shortname . '/edit/';
    }

    public function getPromoURL() {
        return ManagerController::$url . $this->shortname . '/promo/';
    }

    public function getDeleteURL() {
        return ManagerController::$url . $this->shortname . '/delete/';
    }

    public function getDeleteFinalURL() {
        return ManagerController::$url . $this->shortname . '/delete_final/';
    }

    public function getSubscriptionsURL() {
        return ManagerController::$url . $this->shortname . '/subscriptions/';
    }

    public function getUsersURL() {
        return ManagerController::$url . $this->shortname . '/users/';
    }

    public function getCleanupURL() {
        return ManagerController::$url . $this->shortname . '/cleanup/';
    }

    public function getArchiveURL() {
        return ManagerController::$url . $this->shortname . '/archive/';
    }

    public function getSearchURL() {
        return ManagerController::$url . $this->shortname . '/search/';
    }

    public function getBulkActionURL() {
        return $this->getManageURL() . 'bulk/';
    }

    public function getUsers()
    {
        $options = array(
            'calendar_id' => $this->id
        );

        return new Users($options);
    }

    public function hasUser($user = NULL)
    {
        if ($user == NULL) {
            $user = Auth::getCurrentUser();
        }

        $users = $this->getUsers();
        foreach ($users as $cal_user) {
            if ($user->uid == $cal_user->uid) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function getUsersNotOnCalendar()
    {
        $options = array(
            'not_calendar_id' => $this->id
        );

        return new Users($options);
    }

    public function addUser(User $user)
    {
        if (isset($this->id)) {
            $permissions = new Permissions();
            foreach ($permissions as $permission) {
                if (!$user->hasPermission($permission->id, $this->id)) {
                    $user->grantPermission($permission->id, $this->id);
                }
            }
            return true;
        }

        return false;

    }
    
    public function removeUser(User $user)
    {
        if (isset($this->id) && isset($user->uid)) {
            $sql = 'DELETE FROM user_has_permission WHERE user_uid = \''.$user->uid.'\' AND calendar_id ='.$this->id;
            $db = $this->getDB();
            return $db->execute($sql);
        }
        return false;
    }

    public function delete() {
        # delete all events that were created on this calendar
        $events = $this->getEventsCreatedHere(); # we will need to write this method
        foreach ($events as $record) {
           $record->delete();
        }

        # delete the calendar_has_event records
        $has_events = $this->getCalendarHasEvents();
        foreach ($has_events as $record) {
            $record->delete();
        }

        # delete the user_has_permission records
        $permissions = $this->getAllPermissions();
        foreach ($permissions as $record) {
            $record->delete();
        }

        # delete the subscriptions on the calendar
        $subscriptions = $this->getSubscriptions();
        foreach ($subscriptions as $record) {
            $record->delete();
        }

        # delete the subscription_has_calendar records (remove calendar from subscriptions that subscribe to it)
        $subscriptions = $this->getSubscriptionHasCalendarRecords();
        foreach ($subscriptions as $record) {
            $record->delete();
        }

        return parent::delete();
    }
    
    /**
     * Adds the event to the current calendar, and updates subscribed calendars with the same event.
     *
     * @param UNL_UCBCN_Event $event
     * @param string          $status posted | pending | archived
     * @param UNL_UCBCN_User  $user   the user adding this event
     * @param string          $source create event form, subscription.
     *
     * @return int > 0 on success.
     */
    public function addEvent($event, $status, $user, $source = null)
    {
        if (CalendarHasEvent::getByIDs($this->id, $event->id)) {
            # do not add this
        } else {
            $calendar_has_event = new CalendarHasEvent;

            $calendar_has_event->calendar_id = $this->id;
            $calendar_has_event->event_id = $event->id;
            $calendar_has_event->status = $status;
            $calendar_has_event->source = $source;
            $calendar_has_event->insert();
        }

        if ($event->approvedforcirculation) {
            # get the subscribed calendars and similarly add the event to them.
            # we use the insert method instead of reusing addEvent because we do not want an infinite loop
            foreach ($this->getSubscriptionsToThis() as $subscription) {
                # it's confusing, but for each subscription which has this calendar as a 
                # subscribed calendar, take that subscription, find what calendar it is
                # attached to, and add a calendar_has_event record
                if (!($subscription instanceof \UNL\UCBCN\Calendar\Subscription) || CalendarHasEvent::getByIDs($subscription->calendar_id, $event->id)) {
                    # do not add this
                    continue; 
                }

                $calendar_has_event = new CalendarHasEvent;

                $calendar_has_event->calendar_id = $subscription->calendar_id;
                $calendar_has_event->event_id = $event->id;
                $calendar_has_event->status = $subscription->getApprovalStatus();
                $calendar_has_event->source = 'subscription';

                $calendar_has_event->insert();
            }
        }
    }
    
    /**
     * Removes the given event from the calendar_has_event table.
     *
     * @param UNL_UCBCN_Event $event The event to remove from this calendar.
     *
     * @return bool
     */
    public function removeEvent($event, $status)
    {
        # get the Calendar Has Event record
        $calendar_has_event = CalendarHasEvent::getByIdsStatus($this->id, $event->id, $status);

        # check if this is where the event was originally created
        if ($calendar_has_event->source == 'create event form' || $calendar_has_event->source == 'create event api') {
            # delete the event from the entire system
            $event->delete();
        } else {
            # delete the calendar has event record
            $calendar_has_event->delete();
        }
    }

    public function purgePastEventsByStatus($status, $pastDuration) {
        $events = $this->getEvents($status);

        $count = 0;
        foreach ($events as $event) {
            if ($event->isInThePast($pastDuration)) {
                $this->removeEvent($event, $status);
                ++$count;
            }
        }

        return $count;
    }

    public function getPastPostedEventIDs() {
        $sql = 'SELECT distinct event.id FROM calendar_has_event
                INNER JOIN event on event.id = calendar_has_event.event_id
                INNER JOIN eventdatetime on event.id = eventdatetime.event_id
                WHERE calendar_has_event.calendar_id = ' . $this->id . ' AND
                    calendar_has_event.status = "posted" AND
                    eventdatetime.starttime < "' . date('Y-m-d') . ' 00:00:00" AND
                    (eventdatetime.endtime IS NULL OR eventdatetime.endtime < "' . date('Y-m-d') . ' 00:00:00")';

        $eventIDs = array();
        $mysqli = self::getDB();
        if ($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $eventIDs[] = $row['id'];
            }
        }

        return $eventIDs;
    }

    public function archiveEvents($eventIDs = NULL) {

        // process only event ids if provided
        if (is_array($eventIDs)) {

            $events = array();
            $archived_events = array();

            // Lookup events and place in correct array
            foreach($eventIDs as $id) {
                $calenderEvent = CalendarHasEvent::getByIds($this->id, $id);
                switch($calenderEvent->status) {
                    case static::STATUS_POSTED:
                        $events[] = Event::getById($calenderEvent->event_id);
                        break;
                    case static::STATUS_ARCHIVED:
                        $archived_events[] = Event::getById($calenderEvent->event_id);
                        break;
                    default:
                        // ignore event
                }
            }

        } else {
            # find all posted (upcoming) and archived (past) events on the calendar
            $events = $this->getEvents(static::STATUS_POSTED);
            $archived_events = $this->getEvents(static::STATUS_ARCHIVED);
        }

        # check each event to see if it has passed
        $updateEventIDs = array();
        foreach ($events as $event) {
            # remember event id to update status
            if ($event->isInThePast()) {
                $updateEventIDs[] = $event->id;
            }
        }
        if (count($updateEventIDs) > 0) {
            CalendarHasEvent::bulkUpdateStatus($this->id, $updateEventIDs, static::STATUS_POSTED, static::STATUS_ARCHIVED);
        }

        # check each past event to see if it is now current
        $updateEventIDs = array();
        foreach ($archived_events as $event) {
            # remember event id to update status
            if (!$event->isInThePast()) {
                $updateEventIDs[] = $event->id;
            }
        }
        if (count($updateEventIDs) > 0) {
            CalendarHasEvent::bulkUpdateStatus($this->id, $updateEventIDs, static::STATUS_ARCHIVED, static::STATUS_POSTED);
        }
    }

    /**
     * Gets events related to this calendar
     */
    public function getEvents($status = 'all', $limit = -1, $offset = 0) 
    {
        # create options for event listing class
        $options = array(
            'calendar' => $this->shortname,
            'limit' => $limit,
            'offset' => $offset
        );

        if ($status != 'all') {
            $options['status'] = $status;
        }

        # create new events class. On constructor it will get the stuff
        $events = new Events($options);
        return $events;
    }

    public function getEventsCreatedHere()
    {
        # create options for event listing class
        $options = array(
            'calendar' => $this->shortname,
            'created_only' => true
        );

        # create new events class. On constructor it will get the stuff
        $events = new Events($options);
        return $events;
    }

    public function getSubscriptions() 
    {
        return new Calendar\Subscriptions(array('calendar_id' => $this->id));
    }

    public function getSubscriptionsToThis() 
    {
        return new Calendar\Subscriptions(array('subbed_calendar_id' => $this->id));
    }

    public function getSubscriptionHasCalendarRecords()
    {
        return new Calendar\SubscriptionHasCalendars(array('calendar_id' => $this->id));
    }

    public function getAllPermissions()
    {
        return new User\Permissions(array('calendar_id' => $this->id));
    }

    public function getCalendarHasEvents()
    {
        return new CalendarHasEvents(array('calendar_id' => $this->id));
    }

    /**
     * @param Event $event
     * @return false|CalendarHasEvent
     */
    public function hasEvent(Event $event)
    {
        return CalendarHasEvent::getByIds($this->id, $event->id);
    }

    /**
     * Finds the subscriptions this calendar has, and processes them.
     *
     * @return void
     */
    public function processSubscriptions()
    {
        $subscriptions = new Calendar\Subscriptions(array('calendar_id'=>$this->id));
        foreach ($subscriptions as $subscription) {
            $subscriptions->process();
        }
    }
}
