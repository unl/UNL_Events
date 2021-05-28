<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Calendar\Events as CalendarHasEvents;
use UNL\UCBCN\Calendar\EventType;
use UNL\UCBCN\Event\Occurrences;
use UNL\UCBCN\Event\RecurringDate;
use UNL\UCBCN\Event\RecurringDates;
use UNL\UCBCN\EventListing;
use UNL\UCBCN\Manager\Auth;
use UNL\UCBCN\Manager\Controller;
use UNL\UCBCN\Permission;
use UNL\UCBCN\User;

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
    public $promoted;

    const ONE_DAY = 86400;
    const ONE_WEEK = 604800;

    public static function getTable()
    {
        return 'event';
    }

    function keys()
    {
        return array(
            'id',
        );
    }

    // Override Record::synchronizeWithArray to format select event values
    public function synchronizeWithArray($data)
    {
        foreach (get_object_vars($this) as $key=>$default_value) {
            if (isset($data[$key])) {
                switch($key) {
                    case 'listingcontactphone':
                        $this->$key = Util::formatPhoneNumber($data[$key]);
                        break;

                    default:
                        $this->$key = $data[$key];
                }
            }
        }
    }
    
    function getDatetimes($limit = -1, $offset = 0) 
    {
        $options = array(
            'event_id' => $this->id,
            'limit' => $limit,
            'offset' => $offset
        );

        return new EventListing($options);
    }

    public function getStatusWithCalendar(Calendar $calendar)
    {
        $calendar_has_event = CalendarHasEvent::getByIds($calendar->id, $this->id);
        if ($calendar_has_event === FALSE) {
            return NULL;
        } else {
            return $calendar_has_event->status;
        }
    }

    public function updateStatusWithCalendar(Calendar $calendar, $status)
    {
        $calendar_has_event = CalendarHasEvent::getByIds($calendar->id, $this->id);
        if ($calendar_has_event === FALSE) {
            throw new \Exception('Event does not have status with calendar');
        } else {
            $calendar_has_event->status = $status;
            $calendar_has_event->update();
        }
    }

    public function isFeaturedWithCalendar(Calendar $calendar)
    {
        $calendar_has_event = CalendarHasEvent::getByIds($calendar->id, $this->id);
        if ($calendar_has_event === FALSE) {
            return false;
        } else {
            return $calendar_has_event->isFeatured();
        }
    }

    public function updateFeaturedWithCalendar(Calendar $calendar, $featured = FALSE)
    {
        $calendar_has_event = CalendarHasEvent::getByIds($calendar->id, $this->id);
        if ($calendar_has_event === FALSE) {
            throw new \Exception('Event does not have featured with calendar');
        } else {
            $calendar_has_event->featured = ($featured === TRUE) ? 1 : 0;
            $calendar_has_event->update();
        }
    }

    public function isPinnedWithCalendar(Calendar $calendar)
    {
        $calendar_has_event = CalendarHasEvent::getByIds($calendar->id, $this->id);
        if ($calendar_has_event === FALSE) {
            return false;
        } else {
            return $calendar_has_event->isPinned();
        }
    }

    public function updatePinnedWithCalendar(Calendar $calendar, $pinned = FALSE)
    {
        $calendar_has_event = CalendarHasEvent::getByIds($calendar->id, $this->id);
        if ($calendar_has_event === FALSE) {
            throw new \Exception('Event does not have pinned with calendar');
        } else {
            $calendar_has_event->pinned = ($pinned === TRUE) ? 1 : 0;
            $calendar_has_event->update();
        }
    }

    public function getEditURL($calendar = NULL) {
        $user = Auth::getCurrentUser();

        if ($calendar == NULL) {
            // get the origin calendar for the event. If the user has editing permissions there,
            // we will send them to edit it there
            $origin_calendar = $this->getOriginCalendar();
            error_log(print_r($origin_calendar,1));
            if ($this->getStatusWithCalendar($origin_calendar) && $user->hasPermission(Permission::EVENT_EDIT_ID, $origin_calendar->id)) {
                return Controller::$url . $origin_calendar->shortname . '/event/' . $this->id . '/edit/';
            }

            // if that doesn't work, try to have the user edit it from main UNL calendar
            $main_calendar = Calendar::getByID(\UNL\UCBCN::$main_calendar_id);
            if ($this->getStatusWithCalendar($main_calendar) && $user->hasPermission(Permission::EVENT_EDIT_ID, $main_calendar->id)) {
                return Controller::$url . $main_calendar->shortname . '/event/' . $this->id . '/edit/';
            }
        } else {
            return Controller::$url . $calendar->shortname . '/event/' . $this->id . '/edit/';    
        }
        return NULL;        
    }
    
    public function getViewURL($calendar)
    {
        return Controller::$url . $calendar->shortname  . '/event/' . $this->id . '/';
    }

    public function getAddDatetimeURL($calendar) {
        return Controller::$url . $calendar->shortname . '/event/' . $this->id . '/datetime/add/';
    }

    public function getDeleteURL($calendar) {
        return Controller::$url . $calendar->shortname . '/event/' . $this->id . '/delete/';
    }

    public function getRecommendURL($calendar) {
        return Controller::$url . $calendar->shortname . '/event/' . $this->id . '/recommend/';
    }

    public function getMoveURL($calendar) {
        return Controller::$url . $calendar->shortname . '/event/' . $this->id . '/move/';
    }

    public function getPromoteURL($calendar) {
        return Controller::$url . $calendar->shortname . '/event/' . $this->id . '/promote/';
    }

    public function getToggleFeatureEventAttributeURL($calendar) {
        return Controller::$url . $calendar->shortname . '/event/' . $this->id . '/toggle-feature-event-attribute/';
    }

    # events will only have one type. But the database allows them to have more, technically.
    # hence this method is named getFirstType
    #
    # returns an EventType
    public function getFirstType() {
        $first_type = NULL;

        $types = $this->getEventTypes();
        foreach($types as $type) {
            $first_type = $type->getType();
            break;
        }

        return $first_type;
    }

    public function getSource(Calendar $calendar)
    {
        $calendar_has_event = CalendarHasEvent::getByIds($calendar->id, $this->id);
        if ($calendar_has_event != NULL) {
            return $calendar_has_event->source;
        } else {
            return NULL;
        }
    }

    public function deleteRecurrences()
    {
        $options = array(
            'event_id' => $this->id,
            'linked_only' => TRUE
        );
        $recurring_dates = new RecurringDates($options);

        foreach ($recurring_dates as $recurring_date) {
            $recurring_date->delete();
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
                $new_rows[] = array(date('Y-m-d', $this_start), $event_id, $k, 0, 0, $datetime->id);
                // generate more day recurrences for each day of the event, if it is ongoing (i.e., the end date is the next day or later)
                $next_day = strtotime('midnight tomorrow', $this_start);
                while ($next_day <= $this_end) {
                    // add an entry to recurring dates for this eid, the temp date, is ongoing, not unlinked
                    $new_rows[] = array(date('Y-m-d', $next_day), $event_id, $k, 1, 0, $datetime->id);
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
                $recurring_date->event_datetime_id = $row[5];

                $recurring_date->insert();
            }
        }
        return;
    }

    /**
     * Inserts a new event in the database.
     * Optionally may be passed a calendar to immediately add the event to a calendar.
     * (recommended)
     *
     * @return bool
     */
    public function insert($calendar = null, $source = null)
    {
        $this->datecreated = date('Y-m-d H:i:s');
        $this->datelastupdated = date('Y-m-d H:i:s');

        $this->uidcreated = Auth::getCurrentUser()->uid;
        $this->uidlastupdated = Auth::getCurrentUser()->uid;
        $result = parent::insert();

        $status_for_new_event = 'pending';
        if ($calendar->eventreleasepreference == Calendar::EVENT_RELEASE_PREFERENCE_IMMEDIATE) {
            $status_for_new_event = 'posted';
        }

        if (!empty($calendar)) {
            $calendar->addEvent($this, $status_for_new_event, Auth::getCurrentUser(), $source);
        }

        return $result;
    }

    public function considerForMainCalendar()
    {
        $this->addToCalendar(\UNL\UCBCN::$main_calendar_id, 'pending', 'checked consider event');
    }
    
    /**
     * Updates the record for this event in the database.
     *
     * @param mixed $do DataObject
     *
     * @return bool
     */
    public function update()
    {
        $this->uidcreated = Auth::getCurrentUser();
        $this->uidlastupdated = Auth::getCurrentUser();
        $result = parent::update();

        return $result;
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
    public function addToCalendar($calendar_id, $status='pending', $source = null)
    {
        if (CalendarHasEvent::getByIDs($calendar_id, $this->id)) {
            return TRUE;
        }

        $calendar_has_event = new CalendarHasEvent;

        $calendar_has_event->calendar_id = $calendar_id;
        $calendar_has_event->event_id = $this->id;
        $calendar_has_event->uidcreated = Auth::getCurrentUser()->uid;
        $calendar_has_event->datecreated = date('Y-m-d H:i:s');
        $calendar_has_event->datelastupdated = date('Y-m-d H:i:s');
        $calendar_has_event->uidlastupdated = Auth::getCurrentUser()->uid;
        $calendar_has_event->status = $status;

        if (isset($source)) {
            $calendar_has_event->source = $source;
        }

        return $calendar_has_event->insert();
    }
    
    /**
     * 
     */
    public function delete()
    {
        # delete all related eventdatetimes
        $datetimes = $this->getDatetimes();
        foreach ($datetimes as $record) {
            $record->delete();
        }

        # delete the event has eventtype record(s)
        $eventtypes = $this->getEventTypes();
        foreach ($eventtypes as $record) {
            $record->delete();
        }

        # delete all calendar_has_events
        $calendar_has_events = new CalendarHasEvents(array('event_id' => $this->id));
        foreach ($calendar_has_events as $record) {
            $record->delete();
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

    public function isInThePast($pastDuration = NULL) {

        $now = time();
        $pastTime = $now;
        if (!empty($pastDuration)) {
            $pastTime = strtotime($pastDuration);
            if ($pastTime > $now) {
                $pastTime = $now;
            }
        }

        # we will consider it passed if all datetimes are in the past
        $datetimes = $this->getDatetimes();
        $past = TRUE;
        foreach ($datetimes as $datetime) {
            $recurring_dates = $datetime->getAllDates();
            foreach($recurring_dates as $recurring_date) {
                if ($recurring_date->recurringdate >= date('Y-m-d', $pastTime)) {
                    $past = FALSE;
                    break 2;
                }
            }

            if ($datetime->starttime >= date('Y-m-d 00:00:00', $pastTime) || ($datetime->endtime != NULL && $datetime->endtime >= date('Y-m-d 00:00:00', $pastTime))) {
                $past = FALSE;
                break;
            }
        }

        return $past;
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

    public function getOriginCalendar()
    {
        $calendar_has_event = CalendarHasEvent::getByEventIDSource($this->id, 'create event form');
        if ($calendar_has_event) {
            return Calendar::getByID($calendar_has_event->calendar_id);
        } else {
            $calendar_has_event = CalendarHasEvent::getByEventIDSource($this->id, 'create event api');
            if ($calendar_has_event) {
                return Calendar::getByID($calendar_has_event->calendar_id);
            }
        }
        return NULL;
    }
    
    public function userCanEdit($user = NULL)
    {
        if (empty($user)) {
            $user = Auth::getCurrentUser();
        }

        $origin_calendar = $this->getOriginCalendar();
        if ($origin_calendar != NULL && $this->getStatusWithCalendar($origin_calendar) && $user->hasPermission(Permission::EVENT_EDIT_ID, $origin_calendar->id)) {
            return true;
        }

        $main_calendar = Calendar::getByID(\UNL\UCBCN::$main_calendar_id);
        if ($this->getStatusWithCalendar($main_calendar) && $user->hasPermission(Permission::EVENT_EDIT_ID, $main_calendar->id)) {
            return true;
        }

        return false;
    }
}