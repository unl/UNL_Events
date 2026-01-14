<?php
/**
 * This class contains the information needed for viewing the list of range
 * events within the calendar system.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @version   CVS: $id$
 * @link      http://code.google.com/p/unl-event-publisher/
 */
namespace UNL\UCBCN\Frontend;

/**
 * A list of range of events for a calendar.
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Range extends EventListing implements RoutableInterface
{
    /**
     * Calendar \UNL\UCBCN\Calendar Object
     *
     * @var \UNL\UCBCN\Calendar
     */
    public $calendar;

    public $options = array(
        'limit'  => 10,
        'offset' => 0,
    );

    /**
     * Constructs an range event view for this calendar.
     *
     * @param array $options Associative array of options.
     */
    public function __construct($options = array())
    {
        $options['includeEventImageData'] = FALSE;

        parent::__construct($options);
    }

    /**
     * Get the SQL for finding events
     *
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    function getSQL()
    {
        $timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($this->calendar->defaulttimezone);
        if (empty($this->options['from'])) {
            $fromTimestamp = $timezoneDateTime->getTimestamp(date('Y-m-d'));
        } else {
            $fromTimestamp = $timezoneDateTime->getTimestamp($this->options['from']);
        }

        $fromDate = date('Y-m-d', $fromTimestamp);

        if (!empty($this->options['to'])) {
            $toTimestamp = $timezoneDateTime->getTimestamp($this->options['to']);
            $toDate = date('Y-m-d', $toTimestamp);

            // Build UNION query for date range
            $sql = 'SELECT e.id as id, e.recurringdate_id
                    FROM ((
                        SELECT
                            e.id as id,
                            e.event_id AS event_id,
                            recurringdate.recurringdate,
                            e.starttime,
                            e.endtime,
                            recurringdate.id as recurringdate_id
                        FROM eventdatetime as e
                        JOIN recurringdate ON (
                            recurringdate.event_datetime_id = e.id 
                            AND recurringdate.unlinked = 0
                        )
                        WHERE
                            e.recurringtype != "none"
                            AND recurringdate.recurringdate >= "' . $fromDate . '"
                            AND recurringdate.recurringdate <= "' . $toDate . '"
                    ) UNION (
                        SELECT
                            e.id as id,
                            e.event_id AS event_id,
                            NULL as recurringdate,
                            e.starttime,
                            e.endtime,
                            NULL as recurringdate_id
                        FROM eventdatetime as e
                        WHERE
                            e.recurringtype = "none"
                            AND (
                                (e.starttime >= "' . $fromDate . '" AND e.starttime <= "' . $toDate . '" AND e.endtime >= "' . $fromDate . '" AND e.endtime >= "' . $toDate . '") OR
                                (e.starttime >= "' . $fromDate . '" AND e.endtime <= "' . $toDate . '") OR
                                (e.endtime <= "' . $fromDate . '" AND e.endtime <= "' . $toDate . '" AND e.endtime >= "' . $fromDate . '" AND e.endtime <= "' . $toDate . '")
                            )
                    )) AS e
                    JOIN event ON e.event_id = event.id
                    JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                    WHERE
                        calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                        AND calendar_has_event.status IN ("posted", "archived")
                    ORDER BY (
                        IF (e.recurringdate IS NULL,
                            e.starttime,
                            CONCAT(DATE_FORMAT(e.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                        )
                    ) ASC,
                    event.title ASC';
        } else {
            // Build UNION query for start date only
            $sql = 'SELECT e.id as id, e.recurringdate_id
                    FROM ((
                        SELECT
                            e.id as id,
                            e.event_id AS event_id,
                            recurringdate.recurringdate,
                            e.starttime,
                            e.endtime,
                            recurringdate.id as recurringdate_id
                        FROM eventdatetime as e
                        JOIN recurringdate ON (
                            recurringdate.event_datetime_id = e.id 
                            AND recurringdate.unlinked = 0
                        )
                        WHERE
                            e.recurringtype != "none"
                            AND recurringdate.recurringdate >= "' . $fromDate . '"
                    ) UNION (
                        SELECT
                            e.id as id,
                            e.event_id AS event_id,
                            NULL as recurringdate,
                            e.starttime,
                            e.endtime,
                            NULL as recurringdate_id
                        FROM eventdatetime as e
                        WHERE
                            e.recurringtype = "none"
                            AND e.starttime >= "' . $fromDate . '"
                    )) AS e
                    JOIN event ON e.event_id = event.id
                    JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                    WHERE
                        calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                        AND calendar_has_event.status IN ("posted", "archived")
                    ORDER BY (
                        IF (e.recurringdate IS NULL,
                            e.starttime,
                            CONCAT(DATE_FORMAT(e.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                        )
                    ) ASC,
                    event.title ASC';
        }

        if (is_numeric($this->options['limit']) && $this->options['limit'] >= 1) {
            $sql .= ' LIMIT ' . (int)$this->options['limit'];
        }

        return trim($sql);
    }

    /**
     * Get a permanent URL to this object.
     *
     * @return string URL to this specific range.
     */
    public function getURL()
    {
        return $this->generateURL($this->calendar);
    }

    /**
     * Generate an Range URL for a specific calendar
     *
     * @param Calendar $calendar
     * @return string
     */
    public static function generateURL(Calendar $calendar)
    {
        return $calendar->getURL() . 'range/';
    }

    /**
     * Get the month widget for the context's month
     *
     * @return MonthWidget
     */
    public function getMonthWidget()
    {
        return new MonthWidget($this->options);
    }
}
