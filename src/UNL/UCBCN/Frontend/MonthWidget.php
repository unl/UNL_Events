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

    public function getIterator()
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
        $startDateTime = $timezoneDisplay->getDateTimeAddInterval($datePeriod->getStartDate()->format('Y-m-d H:i:s'), '-P2W')->format('Y-m-d H:i:s');
        $endDateTime = $timezoneDisplay->getDateTimeAddInterval($datePeriod->getEndDate()->format('Y-m-d H:i:s'), 'P2W')->format('Y-m-d H:i:s');

        $sql = '
                SELECT DISTINCT e.id as id,recurringdate.recurringdate,e.starttime,e.endtime,e.timezone,event.title, recurringdate.id as recurringdate_id
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (recurringdate.event_datetime_id = e.id AND (recurringdate.recurringdate >= "' . $startDateTime . '" AND recurringdate.recurringdate <= "' . $endDateTime . '") AND recurringdate.unlinked = 0)
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND calendar_has_event.status IN ("posted", "archived")
                    AND  (
                        (e.starttime >= "' . $startDateTime . '" AND e.endtime <= "' . $endDateTime . '")
                       OR (recurringdate.recurringdate >= "' . $startDateTime . '" AND recurringdate.recurringdate <= "' . $endDateTime . '")
                      )
                ORDER BY (
                    IF (recurringdate.recurringdate IS NULL,
                      e.starttime,
                      CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                    )
                ) ASC,
                event.title ASC';

        $db = \UNL\UCBCN\ActiveRecord\Database::getDB();
        $res = $db->query(trim($sql));

        if (!$res) {
            return array();
        }

        return $res;
    }
}
