<?php
namespace UNL\UCBCN;

use UNL\UCBCN\Manager\Auth;
use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Events;
use UNL\UCBCN\Frontend\Controller as FrontendController;
use UNL\UCBCN\Manager\Controller as ManagerController;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
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

    public function getManageURL() {
        return ManagerController::$url . $this->shortname . "/";
    }

    public function getNewURL() {
        return ManagerController::$url . 'calendar/new/';
    }

    public function getEditURL() {
        return ManagerController::$url . $this->shortname . '/edit/';
    }

    public function getSubscriptionsURL() {
        return ManagerController::$url . $this->shortname . '/subscriptions/';
    }

    public function getUsersURL() {
        return ManagerController::$url . $this->shortname . '/users/';
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
        if (isset($this->id)&&isset($user->uid)) {
            $sql = 'DELETE FROM user_has_permission WHERE user_uid = \''.$user->uid.'\' AND calendar_id ='.$this->id;
            $db = $this->getDB();
            return $db->execute($sql);
        }
        return false;
    }

    public function deleteCalendar(Calendar $calendar){
        
        #deletes all the events on the calendar
        foreach ($calendar->getEvents() as $event) {
            $event->delete();
        }
            #deletes the calendar
            $sql = 'DELETE FROM calendar WHERE id =' .$calendar->id;
            $db = $this->getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return true;
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
        $calendar_has_event = new CalendarHasEvent;

        $calendar_has_event->calendar_id = $this->id;
        $calendar_has_event->event_id = $event->id;
        $calendar_has_event->status = $status;
        $calendar_has_event->source = $source;

        $result = $calendar_has_event->insert();

        if ($result && $event->approvedforcirculation) {
            # get the subscribed calendars and similarly add the event to them.
            # we use the insert method instead of reusing addEvent because we do not want an infinite loop
            foreach ($this->getSubscriptionsToThis() as $subscription) {
                # it's confusing, but for each subscription which has this calendar as a 
                # subscribed calendar, take that subscription, find what calendar it is
                # attached to, and add a calendar_has_event record
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
    public function removeEvent(UNL_UCBCN_Event $event)
    {
        if (isset($this->id) && isset($event->id)) {
            $calendar_has_event              = UNL_UCBCN::factory('calendar_has_event');
            $calendar_has_event->calendar_id = $this->id;
            $calendar_has_event->event_id    = $event->id;
            return $calendar_has_event->delete();
        } else {
            return false;
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

    public function getSubscriptions() 
    {
        return new Calendar\Subscriptions(array('calendar_id' => $this->id));
    }

    public function getSubscriptionsToThis() 
    {
        return new Calendar\Subscriptions(array('subbed_calendar_id' => $this->id));
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
