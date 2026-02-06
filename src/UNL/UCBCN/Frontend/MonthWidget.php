<?php
/**
 * This class defines a 30 day widget containing information for a given month.
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

use Traversable;

/**
 * Class defines a month widget, basically a table with 30 boxes representing the
 * days in the month. Days which have events will be selected.
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class MonthWidget extends Month
{
    public $data = array();
    
    /**
     * Constructor for an individual day.
     *
     * @param array $options Associative array of options to apply.
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $this->data = $this->getEventTotals($this->getDatePeriod());
    }

    public function getIterator(): Traversable
    {
        return new \IteratorIterator($this->getDatePeriod());
    }
    
    public function getDayURL(\DateTime $datetime)
    {
        return Day::generateURL($this->calendar, $datetime);
    }
    /**
     * This function finds ongoing events for the given month.
     *
     * @param $datePeriod
     * @internal param \UNL\UCBCN\Frontend\Calendar_Month $month Month to find ongoing events for.
     *
     * @return array
     */
    public function getEventTotals(\DatePeriod $datePeriod)
    {
        $periodEvents = $this->getEvents($datePeriod);
        $results = array();
        foreach ($datePeriod as $dt) {
            $options = array(
                'calendar' => $this->calendar,
                'm' => $dt->format('m'),
                'd' => $dt->format('d'),
                'y' => $dt->format('Y'),
                'periodEvents' => $periodEvents
            );
            $day = new Day($options);
            if (count($day) > 0) {
                $results[$dt->format("Y-m-d")] = count($day);
            }
        }

        return $results;
    }

    public function getEvents(\DatePeriod $datePeriod) {

        $timezoneDisplay = \UNL\UCBCN::getTimezoneDisplay($this->calendar->defaulttimezone);
        $startDateTime = $timezoneDisplay->getDateTimeAddInterval($datePeriod->getStartDate()->format('Y-m-d H:i:s'), '-P2D')->format('Y-m-d H:i:s');
        $endDateTime = $timezoneDisplay->getDateTimeAddInterval($datePeriod->getEndDate()->format('Y-m-d H:i:s'), 'P2D')->format('Y-m-d H:i:s');

        $sql = 'SELECT
                    DISTINCT e.id as id,
                    rd.id as recurringdate_id,
                    rd.recurringdate,
                    e.starttime,
                    e.endtime,
                    e.timezone
                FROM eventdatetime as e
                LEFT JOIN recurringdate as rd ON (
                    e.recurringtype != "none" AND
                    rd.event_datetime_id = e.id AND
                    rd.unlinked = 0 AND
                    rd.ongoing = 0
                )
                WHERE
                    (
                        COALESCE(
                            TIMESTAMP(rd.recurringdate, TIME(e.starttime)),
                            e.starttime
                        ) BETWEEN "' . $startDateTime . '" AND "' . $endDateTime . '"
                    )
                    AND
                    EXISTS (
                        SELECT * FROM calendar_has_event
                        WHERE
                            calendar_has_event.calendar_id = ' . (int)$this->calendar->id . ' AND
                            calendar_has_event.event_id = e.event_id AND
                            calendar_has_event.status IN ("posted", "archived")
                    )
                ORDER BY
                    COALESCE(TIMESTAMP(rd.recurringdate, TIME(e.starttime)), e.starttime) ASC,
                    (SELECT title FROM event WHERE event.id = e.event_id) ASC
                ';

        $db = \UNL\UCBCN\ActiveRecord\Database::getDB();
        $res = $db->query(trim($sql));

        if (!$res) {
            return array();
        }

        return $res;
    }
}
