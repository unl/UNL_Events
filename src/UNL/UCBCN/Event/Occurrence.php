<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Event;
use UNL\UCBCN\Location;
use UNL\UCBCN\Event\RecurringDate;
use UNL\UCBCN\Event\RecurringDates;
use UNL\UCBCN\Manager\Controller;
use UNL\UCBCN\Webcast;

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
    public $location_id;                     // int(10)  multiple_key unsigned
    public $webcast_id;                      // int(10)  multiple_key unsigned
    public $timezone;                        // string(30)
    public $starttime;                       // datetime(19)  multiple_key binary
    public $endtime;                         // datetime(19)  multiple_key binary
    public $recurringtype;                   // string(255)
    public $recurs_until;                    // datetime
    public $rectypemonth;                    // string(255)
    public $room;                            // string(255)
    public $hours;                           // string(255)
    public $directions;                      // blob(4294967295)  blob
    public $additionalpublicinfo;            // blob(4294967295)  blob
    public $location_additionalpublicinfo;   // blob(4294967295)  blob
    public $webcast_additionalpublicinfo;    // blob(4294967295)  blob
    public $canceled;

    const ONE_DAY = 86400;
    const ONE_WEEK = 604800;
    
    const RECURRING_TYPE_NONE = 'none';

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

    public function getDeleteRecurrenceURL($calendar, $id)
    {
        return Controller::$url . $calendar->shortname . '/event/' . $this->event_id . '/datetime/' . $this->id . '/delete/recurrence/' . $id . '/';
    }

    public function isRecurring() 
    {
        return $this->recurringtype != 'none' && $this->recurringtype != NULL;
    }
    
    public function insert()
    {
        $r = parent::insert();
        if ($r) {
            $this->insertRecurrences();
        }
        return $r;
    }
    
    public function update()
    {
        $r = parent::update();
        return $r;
    }
    
    public function delete()
    {
        //delete the actual event.
        $r = parent::delete();
        if ($r) {
            if (isset($this->location_id)) {
                $location = $this->getLocation();
                if ($location !== false && !$location->isSavedOrStandard()) {
                    $location_occurrences = new Occurrences(array(
                        'location_id' => $location->id,
                    ));

                    if (count($location_occurrences) === 0) {
                        $location->delete();
                    }
                }
            }
            if (isset($this->webcast_id)) {
                $webcast = $this->getWebcast();
                if ($webcast !== false && !$webcast->isSaved()) {
                    $webcast_occurrences = new Occurrences(array(
                        'webcast_id' => $webcast->id,
                    ));

                    if (count($webcast_occurrences) === 0) {
                        $webcast->delete();
                    }
                }
            }
            $this->deleteRecurrences();
        }
        return $r;
    }

    public function getRecurrence($recurrence_id)
    {
        return RecurringDate::getByEventDatetimeIDRecurrenceID($this->id, $recurrence_id);
    }

    public function getRecurrences()
    {
        return new RecurringDates(array(
            'event_datetime_id' => $this->id
        ));
    }

    public function getAllDates()
    {
        return new RecurringDates(array(
            'event_datetime_id' => $this->id,
            'with_ongoing' => true
        ));
    }

    public function deleteRecurrences()
    {
        $recurring_dates = $this->getAllDates();

        foreach ($recurring_dates as $recurring_date) {
            $recurring_date->delete();
        }
    }

    public function insertRecurrences()
    {
        if ($this->isRecurring()) {
            $new_rows = array();

            $start_date = strtotime($this->starttime); // Y-m-d H:i:s string -> int
            $end_date = strtotime($this->endtime); // Y-m-d H:i:s string -> int
            $recurring_type = $this->recurringtype;
            $rec_type_month = $this->rectypemonth;
            $recurs_until = strtotime($this->recurs_until); // Y-m-d H:i:s string -> int
            $k = 0; // this counts the recurrence_id, i.e. which recurrence of the event it is
            $this_start = $start_date;
            $this_end = $end_date;
            $length = $end_date - $start_date;
            // while the current start time is before recurs until
            while ($this_start <= $recurs_until) {
                // insert initial day recurrence for this eventdatetime and recurrence, not ongoing, not unlinked
                $new_rows[] = array(date('Y-m-d', $this_start), $this->event_id, $k, 0, 0, $this->id);
                // generate more day recurrences for each day of the event, if it is ongoing (i.e., the end date is the next day or later)
                $next_day = strtotime('midnight tomorrow', $this_start);
                while ($next_day <= $this_end) {
                    // add an entry to recurring dates for this eid, the temp date, is ongoing, not unlinked
                    $new_rows[] = array(date('Y-m-d', $next_day), $this->event_id, $k, 1, 0, $this->id);
                    // increment day
                    $next_day = $next_day + self::ONE_DAY;
                }
                // increment k, which is the recurrence counter (not for the day recurrence, but for the normal recurrence)
                $k++;
                // now we move this_start up, based on the recurrence type, and the while loop sees if that is
                // after the recurs_until
                if ($recurring_type == 'daily') {
                    $this_start += self::ONE_DAY;
                } else if ($recurring_type == 'weekly') {
                    $this_start += self::ONE_WEEK;
                } else if ($recurring_type == 'biweekly') {
                    $this_start += 2 * self::ONE_WEEK;
                } else if ($recurring_type == 'monthly') {
                    // figure out some preliminary things
                    $hour_on_start_date = date('H', $start_date);
                    $minute_on_start_date = date('i', $start_date);
                    $second_on_start_date = date('s', $start_date);
                    $next_month_num = (int)(date('n', $this_start)) + 1;
                    $next_month_year = (int)(date('Y', $this_start));
                    if ($next_month_num > 12) {
                        $next_month_num -= 12;
                        $next_month_year += 1;
                    }
                    $days_in_next_month = cal_days_in_month(CAL_GREGORIAN, $next_month_num, $next_month_year);
                    // now work how to get next month's day
                    if ($rec_type_month == 'date') {
                        $day_for_next_month = min($days_in_next_month, (int)(date('j', $start_date)));
                        $this_start = mktime($hour_on_start_date, $minute_on_start_date, $second_on_start_date, $next_month_num, $day_for_next_month, $next_month_year);
                    } else if ($rec_type_month == 'lastday') {
                        $this_start = mktime($hour_on_start_date, $minute_on_start_date, $second_on_start_date, $next_month_num, $days_in_next_month, $next_month_year);
                    } else if ($rec_type_month != NULL) { // first, second, third, fourth, or last
                        $weekday = date('l', $start_date);
                        $month_name = date('F', strtotime("2015-{$next_month_num}-01"));
                        $this_start = strtotime("{$rec_type_month} {$weekday} of {$month_name} {$next_month_year}");
                        $this_start = strtotime(date('Y-m-d', $this_start) . ' ' . $hour_on_start_date . ':' . $minute_on_start_date . ':' . $second_on_start_date);
                    } else {
                        # don't want an infinite loop
                        break;
                    }
                } else if ($recurring_type == 'annually' || $recurring_type == 'yearly') { 
                    $this_start = strtotime('+1 year', $this_start);
                } else {
                    # don't want an infinite loop
                    break;
                }
                $this_end = $this_start + $length;
            }
            if (!empty($new_rows)) {
                foreach ($new_rows as $row) {
                    $recurring_date = new RecurringDate;
                    $recurring_date->canceled = 0;
                    $recurring_date->recurringdate = $row[0];
                    $recurring_date->event_id = $row[1];
                    $recurring_date->recurrence_id = $row[2];
                    $recurring_date->ongoing = $row[3];
                    $recurring_date->unlinked = $row[4];
                    $recurring_date->event_datetime_id = $row[5];

                    $recurring_date->insert();
                }
            }
        }
        return;
    }
    
    /**
     * Gets an object for the location of this event date and time if set.
     *
     * @return UNL\UCBCN\Location|false
     */
    public function getLocation()
    {
        if (isset($this->location_id)) {
            return Location::getById($this->location_id);
        }

        return false;
    }

    /**
     * Gets an object for the webcast of this event date and time if set.
     *
     * @return UNL\UCBCN\Webcast|false
     */
    public function getWebcast()
    {
        if (isset($this->webcast_id)) {
            return Webcast::getById($this->webcast_id);
        }

        return false;
    }

    /**
     * Get the event
     *
     * @return \UNL\UCBCN\Event
     */
    public function getEvent($includeImageData = FALSE)
    {
        $fields = array_keys(get_class_vars(get_class(new Event)));
        if ($includeImageData === FALSE) {
            $fields = array_diff($fields, array('imagedata'));
        }
        return Event::getById($this->event_id, NULL, $fields);
    }

    public function isCanceled() {
        return !empty($this->canceled);
    }

    // This will use google microdata's requirements and check if the occurrence is valid
    public function microdataCheck()
    {
        // We need a start time
        if (!isset($this->starttime) || empty($this->starttime)) {
            return false;
        }

        // We need at least a location or a virtual location or both
        if (!isset($this->location_id) && !isset($this->webcast_id)) {
            return false;
        }

        // Check if the location valid
        if (isset($this->location_id)) {
            $location = $this->getLocation();
            if ($location !== false && !$location->microdataCheck()) {
                return false;
            }
        }

        // Check if the virtual location is valid
        if (isset($this->webcast_id)) {
            $webcast = $this->getWebcast();
            if ($webcast !== false && !$webcast->microdataCheck()) {
                return false;
            }
        }

        return true;
    }
}
