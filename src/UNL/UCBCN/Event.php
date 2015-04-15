<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event\Occurrences;
use UNL\UCBCN\Event\RecurringDate;

/**
 * Table Definition for event
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
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Event extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $title;                           // string(100)  not_null multiple_key
    public $subtitle;                        // string(100)
    public $othereventtype;                  // string(255)
    public $description;                     // blob(4294967295)  blob
    public $shortdescription;                // string(255)
    public $refreshments;                    // string(255)
    public $classification;                  // string(100)
    public $approvedforcirculation;          // int(1)
    public $transparency;                    // string(255)
    public $status;                          // string(100)
    public $privatecomment;                  // blob(4294967295)  blob
    public $otherkeywords;                   // string(255)
    public $imagetitle;                      // string(100)
    public $imageurl;                        // blob(4294967295)  blob
    public $webpageurl;                      // blob(4294967295)  blob
    public $listingcontactuid;               // string(255)
    public $listingcontactname;              // string(100)
    public $listingcontactphone;             // string(255)
    public $listingcontactemail;             // string(255)
    public $icalendar;                       // blob(4294967295)  blob
    public $imagedata;                       // blob(4294967295)  blob binary
    public $imagemime;                       // string(255)
    public $datecreated;                     // datetime(19)  binary
    public $uidcreated;                      // string(100)
    public $datelastupdated;                 // datetime(19)  binary
    public $uidlastupdated;                  // string(100)

    const ONE_DAY = 86400;
    const ONE_WEEK = 604800;

    public static function getTable()
    {
        return 'event';
    }

    /**
     * Returns an associative array of the fields for this table.
     *
     * @return array
     */
    public function table()
    {
        $table = array(
            'id'=>129,
            'title'=>130,
            'subtitle'=>2,
            'othereventtype'=>2,
            'description'=>66,
            'shortdescription'=>2,
            'refreshments'=>2,
            'classification'=>2,
            'approvedforcirculation'=>17,
            'transparency'=>2,
            'status'=>2,
            'privatecomment'=>66,
            'otherkeywords'=>2,
            'imagetitle'=>2,
            'imageurl'=>66,
            'webpageurl'=>66,
            'listingcontactuid'=>2,
            'listingcontactname'=>2,
            'listingcontactphone'=>2,
            'listingcontactemail'=>2,
            'icalendar'=>66,
            'imagedata'=>66,
            'imagemime'=>2,
            'datecreated'=>14,
            'uidcreated'=>2,
            'datelastupdated'=>14,
            'uidlastupdated'=>2
        );

        return $table;

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
        return array('listingcontactuid' => 'users:uid',
                     'uidcreated'        => 'users:uid',
                     'uidlastupdated'    => 'users:uid');
    }

    public function getStatusWithCalendar(Calendar $calendar) {
        $calendar_has_event = CalendarHasEvent::getById($calendar->id, $this->id);
        if ($calendar_has_event === FALSE) {
            return NULL;
        } else {
            return $calendar_has_event->status;
        }
    }
    
    /**
     * This function processes any posted files,
     * sepcifically the images for an event.
     *
     * Called from insert() or update().
     *
     * @return void
     */
    public function processFileAttachments()
    {
        if (isset($_FILES['imagedata'])
            && is_uploaded_file($_FILES['imagedata']['tmp_name'])
            && $_FILES['imagedata']['error']==UPLOAD_ERR_OK) {
            global $_UNL_UCBCN;
            $this->imagemime = $_FILES['imagedata']['type'];
            $this->imagedata = file_get_contents($_FILES['imagedata']['tmp_name']);
        }
    }

    public function deleteRecurrences()
    {
        $options = array(
            'event_id' => $this->id,
            'recurring_only' => true
        );
        $event_date_times = new Occurrences($options);

        foreach ($event_date_times as $datetime) {
            $datetime->delete();
        }

        return;
    }

    public function insertRecurrences()
    {
        $event_id = (int)($this->id);
        $new_rows = array();

        $options = array(
            'event_id' => $this->id,
            'recurring_only' => true
        );
        $event_date_times = new Occurrences($options);
        foreach ($event_date_times as $datetime) {
            $start_date = strtotime($datetime->starttime); // Y-m-d H:i:s string -> int
            $end_date = strtotime($datetime->endtime); // Y-m-d H:i:s string -> int
            $recurring_type = $datetime->recurringtype;
            $rec_type_month = $datetime->rectypemonth;
            $recurs_until = strtotime($datetime->recurs_until); // Y-m-d H:i:s string -> int
            $k = 0; // this counts the recurrence_id, i.e. which recurrence of the event it is
            $this_start = $start_date;
            $this_end = $end_date;
            $length = $end_date - $start_date;
            // while the current start time is before recurs until
            while ($this_start <= $recurs_until) {
                // insert initial day recurrence for this eventdatetime and recurrence, not ongoing, not unlinked
                $new_rows[] = array(date('Y-m-d', $this_start), $event_id, $k, 0, 0);
                // generate more day recurrences for each day of the event, if it is ongoing (i.e., the end date is the next day or later)
                $next_day = strtotime('midnight tomorrow', $this_start);
                while ($next_day <= $this_end) {
                    // add an entry to recurring dates for this eid, the temp date, is ongoing, not unlinked
                    $new_rows[] = array(date('Y-m-d', $next_day), $event_id, $k, 1, 0);
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
        }
        if (!empty($new_rows)) {
            foreach ($new_rows as $row) {
                $recurring_date = new RecurringDate;
                $recurring_date->recurringdate = $row[0];
                $recurring_date->event_id = $row[1];
                $recurring_date->recurrence_id = $row[2];
                $recurring_date->ongoing = $row[3];
                $recurring_date->unlinked = $row[4];

                $recurring_date->insert();
            }
        }
        return;
    }

    /**
     * Inserts a new event in the database.
     *
     * @return bool
     */
    public function insert()
    {
        $this->processFileAttachments();

        $this->datecreated = date('Y-m-d H:i:s');
        $this->datelastupdated = date('Y-m-d H:i:s');

        $this->uidcreated = $_SESSION['__SIMPLECAS']['UID'];
        $this->uidlastupdated = $_SESSION['__SIMPLECAS']['UID'];
        $result = parent::insert();

        return $result;
    }
    
    /**
     * Updates the record for this event in the database.
     *
     * @param mixed $do DataObject
     *
     * @return bool
     */
    public function update($do=false)
    {
        global $_UNL_UCBCN;
        $GLOBALS['event_id'] = $this->id;
        if (isset($this->consider)) {
            // The user has checked the 'Please consider this event for the main calendar'
            $add_to_default = $this->consider;
            unset($this->consider);
        } else {
            $add_to_default = 0;
        }
        if (is_object($do) && isset($do->consider)) {
            unset($do->consider);
        }
        $this->datelastupdated = date('Y-m-d H:i:s');
        if (isset($_SESSION['_authsession'])) {
            $this->uidlastupdated=$_SESSION['_authsession']['username'];
        }
        $this->processFileAttachments();
        $res = parent::update();
        if ($res) {
            if ($add_to_default && isset($_UNL_UCBCN['default_calendar_id'])) {
                // Add this as a pending event to the default calendar.
                $che = UNL_UCBCN::factory('calendar_has_event');
                $che->calendar_id = $_UNL_UCBCN['default_calendar_id'];
                $che->event_id = $this->id;
                if ($che->find()==0) {
                    $this->addToCalendar($_UNL_UCBCN['default_calendar_id'], 'pending', 'checked consider event');
                }
            }
            //loop though all eventdateandtime instances for this event.
            $events = UNL_UCBCN_Manager::factory('eventdatetime');
            $events->whereAdd('eventdatetime.event_id = '.$this->id);
            $number = $events->find();
            while ($events->fetch()) {
                $facebook = new \UNL\UCBCN\Facebook\Instance($events->id);
                $facebook->updateEvent();
                
            }
        }
        return $res;
    }
    
    /**
     * This function will add the current event to the default calendar.
     * It assumes that the global default_calendar_id is set.
     *
     * @param int    $calendar_id ID of the calendar to add the event to
     * @param string $status      Status to add as, pending | posted | archived
     * @param string $sourcemsg   Message for the source of this addition.
     *
     * @return int|false
     */
    public function addToCalendar($calendar_id, $status='pending', $sourcemsg = null)
    {
        $calendar_has_event = new CalendarHasEvent;

        $calendar_has_event->calendar_id = $this->id;
        $calendar_has_event->event_id = $this->id;
        $calendar_has_event->uidcreated = $_SESSION['__SIMPLECAS']['UID'];
        $calendar_has_event->datecreated = date('Y-m-d H:i:s');
        $calendar_has_event->datelastupdated = date('Y-m-d H:i:s');
        $calendar_has_event->uidlastupdated = $_SESSION['__SIMPLECAS']['UID'];
        $calendar_has_event->status = $status;

        if (isset($source)) {
            $calendar_has_event->source = $source;
        }

        return $calendar_has_event->insert();
    }
    
    /**
     * Performs a delete of this event and all child records
     *
     * @return bool
     */
    public function delete()
    {
        //get all facebook events for this id and delete them.
            $events = UNL_UCBCN_Manager::factory('eventdatetime');
            $events->whereAdd('eventdatetime.event_id = '.$this->id);
            $number = $events->find();
            while ($events->fetch()) {
                $facebook = new \UNL\UCBCN\Facebook\Instance($events->id);
                $facebook->deleteEvent();
            }
          
        // Delete child elements that would be orphaned.
        if (ctype_digit($this->id)) {
            foreach (array('calendar_has_event',
                           'event_has_keyword',
                           'eventdatetime',
                           'event_has_eventtype',
                           'event_has_sponsor',
                           'event_isopento_audience',
                           'event_targets_audience') as $table) {
                self::getDB()->query('DELETE FROM '.$table.' WHERE event_id = '.$this->id);
            }
        }
        return parent::delete();
    }
    
    /**
     * Check whether this event belongs to any calendars.
     *
     * @return bool
     */
    public function isOrphaned()
    {
        if (isset($this->id)) {
            $calendar_has_event = UNL_UCBCN::factory('calendar_has_event');
            $calendar_has_event->event_id = $this->id;
            return !$calendar_has_event->find();
        } else {
            return false;
        }
    }
    
    /**
     * Adds other information to the array produced by $event->toArray().
     * If $event already contains these values, this function can be called with
     * just the first parameter. Otherwise the values must be supplied.
     * 
     * @param UNL_UCBCN_Event $event  the event to call toArray() on
     * @param mixed           $ucee   optional whether the current user can edit $event
     * @param mixed           $ucde   optional whether the user can delete $event
     * @param mixed           $che    optional status if calendar has event, false otherwise
     * @param mixed           $rec_id optional recurrence_id
     * 
     * @return array
     */
    public function eventToArray($event, $ucee=null, $ucde=null, $che=null, $rec_id=false)
    {
        if ($ucee === null && $ucde === null && $che === null) {
            // assume these values to be supplied by $event
            $ucee = $event->usercaneditevent;
            $ucde = $event->usercandeleteevent;
            $che  = $event->calendarhasevent;
            if (isset($event->recurrence_id)) {
                $rec_id = $event->recurrence_id;
            }
        }
        $other_event_info = array(
            'usercaneditevent'=>$ucee,
            'usercandeleteevent'=>$ucde,
            'calendarhasevent'=>$che
        );
        if ($rec_id !== false) {
            $other_event_info['recurrence_id'] = $rec_id;
        }
        $event = array_merge($event->toArray(), $other_event_info);
        return $event;
    }
    
    /**
     * Get event_has_eventtype records for this event
     *
     * @return Event\EventTypes
     */
    public function getEventTypes()
    {
        return new Event\EventTypes(array('event_id' => $this->id));
    }

    /**
     * Get all public contacts for this event
     *
     * @return Event\PublicContacts
     */
    public function getPublicContacts()
    {
        return new Event\PublicContacts(array('event_id' => $this->id));
    }

    /**
     * Get all webcasts for this event
     *
     * @return Event\Webcasts
     */
    public function getWebcasts()
    {
        return new Event\Webcasts(array('event_id' => $this->id));
    }

    /**
     * Get documents for this event
     *
     * @return Event\Documents
     */
    public function getDocuments()
    {
        return new Event\Documents(array('event_id' => $this->id));
    }
}