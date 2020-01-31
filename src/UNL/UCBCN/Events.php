<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;
/**
 * Object related to a list of events.
 * 
 * PHP version 5
 * 
 * @category  Events 
 * @package   UNL_UCBCN
 * @author    Tyler Lemburg <trlemburg@gmail.com>
 * @copyright 2015 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

class Events extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Event',
        );
    }

    public function getSQL() {
        if (array_key_exists('subscription_calendars', $this->options)) {
            if (empty(count($this->options['subscription_calendars']))) {
                // empty array so add invalid calendar id so query works and returns no results
                array_push($this->options['subscription_calendars'], -1);
            }

            // Note: subscription events are limited to those within a day ago and created by calendar
            $sql = 'SELECT DISTINCT event.id FROM event
                    INNER JOIN calendar_has_event ON event.id = calendar_has_event.event_id
                    INNER JOIN eventdatetime ON event.id = eventdatetime.event_id
                    WHERE calendar_has_event.status != "pending" AND
                        calendar_has_event.source  IN ("create event form", "create event api") AND
                        event.approvedforcirculation = 1 AND (
                        (eventdatetime.recurringtype = "none" AND eventdatetime.starttime >= NOW() - INTERVAL 1 DAY) OR
                        (eventdatetime.recurringtype != "none" AND eventdatetime.recurs_until >= NOW() - INTERVAL 1 DAY)) AND (';

            # add the calendars requested
            $sql .= implode(' OR ', array_map(function($calendar_id) {return 'calendar_has_event.calendar_id = ' . (int)$calendar_id;}, $this->options['subscription_calendars']));

            # exclude the calendar that is doing the subscribing (exclude events that it already contains)
            $sql .= ") AND event.id NOT IN (SELECT DISTINCT event.id FROM event, calendar_has_event AS c2 WHERE c2.calendar_id = " . 
                $this->options['subscription_calendar'] . " AND c2.event_id = event.id);";

            return $sql;
        } else if (array_key_exists('created_only', $this->options)) {
            # get all events related to the calendar through a join on calendar has event and calendar.
            $sql = '
                SELECT event.id FROM event
                INNER JOIN calendar_has_event ON event.id = calendar_has_event.event_id
                INNER JOIN calendar ON calendar_has_event.calendar_id = calendar.id
                WHERE calendar.shortname = "' . self::escapeString($this->options['calendar']) . '"
                AND calendar_has_event.source IN ("create event form", "create event api")';

            $sql .= ' GROUP BY event.id ';
            $sql .= ';';

            return $sql;
        
        } else if (array_key_exists('calendar', $this->options)) {
            // Set sort order based on status
            $sortOrder = 'ASC';
            if ($this->options['status'] == \UNL\UCBCN\Calendar::STATUS_ARCHIVED) {
                $sortOrder = 'DESC';
            }

            # get all events related to the calendar through a join on calendar has event and calendar.
            $sql = '
                SELECT event.id FROM event
                INNER JOIN eventdatetime ON event.id = eventdatetime.event_id
                INNER JOIN calendar_has_event ON event.id = calendar_has_event.event_id
                INNER JOIN calendar ON calendar_has_event.calendar_id = calendar.id
                WHERE calendar.shortname = "' . self::escapeString($this->options['calendar']) . '"';
            if (array_key_exists('status', $this->options)) {
                $sql .= ' AND calendar_has_event.status = "' . self::escapeString($this->options['status']) . '"';
            }

            $sql .= ' GROUP BY event.id ';
            $sql .= ' ORDER BY MIN(eventdatetime.starttime) ' . $sortOrder;
            $sql .= ';';

            return $sql;
        } else if (array_key_exists('search_term', $this->options)) {
            $term = $this->options['search_term'];

            $sql = '
                SELECT event.id
                FROM eventdatetime
                INNER JOIN event ON eventdatetime.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN event_has_eventtype ON (event_has_eventtype.event_id = event.id)
                LEFT JOIN eventtype ON (eventtype.id = event_has_eventtype.eventtype_id)
                LEFT JOIN location ON (location.id = eventdatetime.location_id)
                WHERE
                    event.approvedforcirculation = 1
                    AND  (';

            if ($time = strtotime($term)) {
                // This is a time...
                $sql .= 'eventdatetime.starttime LIKE \''.date('Y-m-d', $this->escapeString($time)).'%\'';
            } else {
                // Do a textual search.
                $sql .=
                    '(event.title LIKE \'%'.self::escapeString($term).'%\' OR '.
                    'eventtype.name LIKE \'%'.self::escapeString($term).'%\' OR '.
                    'event.description LIKE \'%'.self::escapeString($term).'%\' OR '.
                    'location.name LIKE \'%'.self::escapeString($term).'%\') ';
            }

            $sql.= ')
                GROUP BY event.id, eventdatetime.starttime
                ORDER BY eventdatetime.starttime DESC, event.title ASC';
            $sql.=';';

            return $sql;
        } else {
            return parent::getSQL();
        }
    }
}
