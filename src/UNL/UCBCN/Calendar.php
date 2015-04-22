<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Events;
use UNL\UCBCN\Frontend\Controller as FrontendController;
use UNL\UCBCN\Manager\Controller as ManagerController;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
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

    public static function getTable()
    {
        return 'calendar';
    }

    function table()
    {
        return array(
            'id'=>129,
            'account_id'=>129,
            'name'=>2,
            'shortname'=>2,
            'website'=>2,
            'eventreleasepreference'=>2,
            'calendardaterange'=>1,
            'formatcalendardata'=>66,
            'uploadedcss'=>66,
            'uploadedxsl'=>66,
            'emaillists'=>66,
            'calendarstatus'=>2,
            'datecreated'=>14,
            'uidcreated'=>2,
            'datelastupdated'=>14,
            'uidlastupdated'=>2,
            'externalforms'=>2,
            'recommendationswithinaccount'=>17,
        	'theme'=>2,
        );
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
    function sequenceKey()
    {
        return array('id',true);
    }
    
    function links()
    {
        return array('account_id'     => 'account:id',
                     'uidcreated'     => 'users:uid',
                     'uidlastupdated' => 'users:uid');
    }

    public function getFrontendURL() {
        return FrontendController::$url . '?calendar_id=' . $this->id;
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

    /**
     * Adds a user to the calendar. Grants all permissions to the
     * user for the current calendar.
     *
     * @param UNL\UCBCN\User $user
     */
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
    
    /**
     * Removes a user from the current calendar.
     * Basically removes all permissions for the user on the current calendar.
     *
     * @param \UNL\UCBCN\User $user
     */
    public function removeUser(User $user)
    {
        if (isset($this->id)&&isset($user->uid)) {
            $sql = 'DELETE FROM user_has_permission WHERE user_uid = \''.$user->uid.'\' AND calendar_id ='.$this->id;
            $db = $this->getDB();
            return $db->execute($sql);
        }
        return false;
    }
    
    /**
     * Adds the event to the current calendar.
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
        $calendar_has_event->uidcreated = $user->uid;
        $calendar_has_event->datecreated = date('Y-m-d H:i:s');
        $calendar_has_event->datelastupdated = date('Y-m-d H:i:s');
        $calendar_has_event->uidlastupdated = $user->uid;
        $calendar_has_event->status = $status;

        if (isset($source)) {
            $calendar_has_event->source = $source;
        }

        return $calendar_has_event->insert();
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
    public function getEvents() {
        # create options for event listing class
        $options = array('calendar' => $this->shortname);

        # create new events class. On constructor it will get the stuff
        $events = new Events($options);
        return $events;
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
