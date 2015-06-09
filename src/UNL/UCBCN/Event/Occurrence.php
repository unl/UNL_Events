<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Event;
use UNL\UCBCN\Location;
use UNL\UCBCN\Event\RecurringDates;
use UNL\UCBCN\Manager\Controller;


/**
 * Table Definition for eventdatetime
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
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Occurrence extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $event_id;                        // int(10)  not_null multiple_key unsigned
    public $location_id;                     // int(10)  not_null multiple_key unsigned
    public $starttime;                       // datetime(19)  multiple_key binary
    public $endtime;                         // datetime(19)  multiple_key binary
    public $recurringtype;                   // string(255)
    public $recurs_until;                    // datetime
    public $rectypemonth;                    // string(255)
    public $room;                            // string(255)
    public $hours;                           // string(255)
    public $directions;                      // blob(4294967295)  blob
    public $additionalpublicinfo;            // blob(4294967295)  blob

    const ONE_DAY = 86400;
    const ONE_WEEK = 604800;

    public static function getTable()
    {
        return 'eventdatetime';
    }

    function keys()
    {
        return array(
            'id',
        );
    }

    public function getEditURL($calendar)
    {
        return Controller::$url . $calendar->shortname . '/event/' . $this->event_id . '/datetime/' . $this->id . '/edit/';
    }

    public function getEditRecurrenceURL($calendar, $id)
    {
        return Controller::$url . $calendar->shortname . '/event/' . $this->event_id . '/datetime/' . $this->id . '/edit/recurrence/' . $id . '/';
    }

    public function getDeleteURL($calendar)
    {
        return Controller::$url . $calendar->shortname . '/event/' . $this->event_id . '/datetime/' . $this->id . '/delete/';
    }
    
    public function insert()
    {
        $r = parent::insert();
        if ($r) {
            $this->getEvent()->insertRecurrences();
        }
        return $r;
    }
    
    public function update()
    {
        $r = parent::update();
        if ($r) {
            $this->getEvent()->deleteRecurrences();
            $this->getEvent()->insertRecurrences();
        }
        return $r;
    }
    
    public function delete()
    {
        //delete the actual event.
        $r = parent::delete();
        if ($r) {
            $this->getEvent()->deleteRecurrences();
        }
        return $r;
    }

    public function getRecurrences()
    {
        return new RecurringDates(array(
            'event_datetime_id' => $this->id
        ));
    }
    
    /**
     * Gets an object for the location of this event date and time.
     *
     * @return UNL\UCBCN\Location
     */
    public function getLocation()
    {
        return Location::getById($this->location_id);
    }

    /**
     * Get the event
     *
     * @return \UNL\UCBCN\Event
     */
    public function getEvent()
    {
        return Event::getById($this->event_id);
    }
}
