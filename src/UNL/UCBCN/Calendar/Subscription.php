<?php
namespace UNL\UCBCN\Calendar;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendars;
use UNL\UCBCN\Events;
use UNL\UCBCN\Manager\Controller as ManagerController;
use UNL\UCBCN\Manager\Auth;

/**
 * Table Definition for subscription
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
class Subscription extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $calendar_id;                     // int(10)  not_null multiple_key unsigned
    public $name;                            // string(100)
    public $automaticapproval;               // int(1)  not_null
    public $timeperiod;                      // date(10)  binary
    public $expirationdate;                  // date(10)  binary
    public $datecreated;                     // datetime(19)  binary
    public $uidcreated;                      // string(100)
    public $datelastupdated;                 // datetime(19)  binary
    public $uidlastupdated;                  // string(100)

    public static function getTable()
    {
        return 'subscription';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
    public function getNewURL($calendar) {
        return ManagerController::$url . $calendar->shortname . '/subscriptions/new/';
    }

    public function getEditURL() {
        $calendar = $this->getCalendar();
        return ManagerController::$url . $calendar->shortname . '/subscriptions/' . $this->id . '/edit/';
    }

    public function getCalendar() {
        return Calendar::getByID($this->calendar_id);
    }

    public function getSubscribedCalendars()
    {
        $options = array(
            'subscription_id' => $this->id
        );
        return new Calendars($options);
    }

    /**
     * Inserts a record into the subscription table, and processes the subscription
     * for matching events.
     *
     * @return int ID of inserted record on success.
     */
    public function insert()
    {
        $this->datecreated = date('Y-m-d H:i:s');
        $this->uidcreated = Auth::getCurrentUser()->uid;
        $this->datelastupdated = date('Y-m-d H:i:s');
        $this->uidlastupdated = Auth::getCurrentUser()->uid;
        $result = parent::insert();

        return $result;
    }
    
    /**
     * Performs an update on this subscription. This will re-evaluate all the events
     * to see if they match the subscription and add them in.
     *
     * Calls self::process() if update was successful.
     *
     * @return bool true on success
     */
    public function update()
    {
        $this->datelastupdated = date('Y-m-d H:i:s');
        $this->uidlastupdated = Auth::getCurrentUser()->uid;
        $result = parent::update();

        return $result;
    }
    
    /**
     * Processes this subscription and adds events not currently
     * added to the calendar this subscription is for.
     *
     * @param int $event_id Optionally only add the event with the mathcing id.
     *
     * @return int number of events added to the calendar
     */
    public function process($event_id = null)
    {
        $status = $this->getApprovalStatus();
        foreach ($this->matchingEvents() as $event) {
            $this->getCalendar()->addEvent($event, $status, Auth::getCurrentUser(), 'subscription');
        }
    }
    
    /**
     * returns the string equivalent of the automatic approval status, for
     * inserting into the calendar_has_event database.
     * It will return 'posted' if automatic approval is true.
     * 'pending' otherwise.
     *
     * @return string the Status.
     */
    public function getApprovalStatus()
    {
        if ($this->automaticapproval==1) {
            return 'posted';
        }

        return 'pending';
    }
    
    /**
     * Finds the events matching this subscription.
     */
    public function matchingEvents()
    {
        $calendars = $this->getSubscribedCalendars();
        $calendar_ids = array();
        foreach ($calendars as $calendar) {
            $calendar_ids[] = $calendar->id;
        }

        $options = array(
            'subscription_calendars' => $calendar_ids,
            'subscription_calendar' => $this->calendar_id
        );
        return new Events($options);
    }

}
