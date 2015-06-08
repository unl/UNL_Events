<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Event;
use UNL\UCBCN\Location;
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
        $rows = array();

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
            $rows[] = array(date('Y-m-d', $this_start), $this->event_id, $k, 0, 0);
            // increment k, which is the recurrence counter (not for the day recurrence, but for the normal recurrence)
            $k++;
            // now we move this_start up, based on the recurrence type, and the while loop sees if that is
            // after the recurs_until
            if ($recurring_type == 'daily') {
                $this_start += self::ONE_DAY;
            } else if ($recurring_type == 'weekly') {
                $this_start += self::ONE_WEEK;
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
                } else { // first, second, third, fourth, or last
                    $weekday = date('l', $start_date);
                    $month_name = date('F', strtotime("2015-{$next_month_num}-01"));
                    $this_start = strtotime("{$rec_type_month} {$weekday} of {$month_name} {$next_month_year}");
                    $this_start = strtotime(date('Y-m-d', $this_start) . ' ' . $hour_on_start_date . ':' . $minute_on_start_date . ':' . $second_on_start_date);
                }
            } else if ($recurring_type == 'annually' || $recurring_type == 'yearly') { 
                $this_start = strtotime('+1 year', $this_start);
            } else {
                // dont want an infinite loop
                break;
            }
            $this_end = $this_start + $length;
        }

        return $rows;
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
