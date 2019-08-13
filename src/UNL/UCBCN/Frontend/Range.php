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

        if (!empty($this->options['to'])) {
            $toTimestamp = $timezoneDateTime->getTimestamp($this->options['to']);

            $eventSQL = '(recurringdate.recurringdate IS NULL AND (
                            (e.starttime >= "'.date('Y-m-d', $fromTimestamp).'" AND e.starttime <= "'.date('Y-m-d', $toTimestamp).'" AND e.endtime >= "'.date('Y-m-d', $fromTimestamp).'" AND e.endtime >= "'.date('Y-m-d', $toTimestamp).'") OR
                            (e.starttime >= "'.date('Y-m-d', $fromTimestamp).'" AND e.endtime <= "'.date('Y-m-d', $toTimestamp).'") OR
                            (e.endtime <= "'.date('Y-m-d', $fromTimestamp).'" AND e.endtime <= "'.date('Y-m-d', $toTimestamp).'" AND e.endtime >= "'.date('Y-m-d', $fromTimestamp).'" AND e.endtime <= "'.date('Y-m-d', $toTimestamp).'")
                        )) OR 
                        (recurringdate.recurringdate IS NOT NULL AND (
                            (recurringdate.recurringdate >= "'.date('Y-m-d', $fromTimestamp).'" AND recurringdate.recurringdate <= "'.date('Y-m-d', $toTimestamp).'")
                        ))';
        } else {

            $eventSQL = "IF (recurringdate.recurringdate IS NULL, e.starttime, recurringdate.recurringdate) >=  '" .date('Y-m-d', $fromTimestamp) . "'";
        }


        $sql = '
                SELECT e.id as id, recurringdate.id as recurringdate_id
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (recurringdate.event_datetime_id = e.id AND recurringdate.unlinked = 0)
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND (
                         calendar_has_event.status =\'posted\'
                         OR calendar_has_event.status =\'archived\'
                    )
                    AND (' . $eventSQL . ')
                ORDER BY (
                        IF (recurringdate.recurringdate IS NULL,
                          e.starttime,
                          CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                        )
                    ) ASC,
                    event.title ASC';

        if (is_numeric($this->options['limit'] && $this->options['limit'] >= 1)) {
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
