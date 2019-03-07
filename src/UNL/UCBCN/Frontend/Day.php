<?php
namespace UNL\UCBCN\Frontend;

use UNL\UCBCN\RuntimeException;

/**
 * This class contains the information needed for viewing a single day view calendar.
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

/**
 * Object for the view of a single day for a calendar.
 * 
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Day extends EventListing implements RoutableInterface
{
    /**
     * Constructor for an individual day.
     * 
     * @param array $options Associative array of options to apply.
     */
    public function __construct($options)
    {
        // Set defaults
        $this->options['m'] = date('m');
        $this->options['d'] = date('d');
        $this->options['y'] = date('Y');
        if (!isset($this->options['includeEventImageData'])) {
            $this->options['includeEventImageData'] = TRUE;
        }

        parent::__construct($options);
    }

    /**
     * Get the SQL for finding events
     * 
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    function getSQL()
    {
        $date = $this->getDateTime()->format('Y-m-d');
        $sql = '
                SELECT DISTINCT e.id as id,recurringdate.recurringdate,e.starttime,event.title, recurringdate.id as recurringdate_id
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (recurringdate.event_datetime_id = e.id AND recurringdate.recurringdate = "' . $date . '" AND recurringdate.unlinked = 0)
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND calendar_has_event.status IN ("posted", "archived")
                    AND  (
                        "' . $date . '" BETWEEN DATE(e.starttime) AND IF(DATE(e.endtime), DATE(e.endtime), DATE(e.starttime))
                       OR "' . $date . '" = recurringdate.recurringdate
                      )
                ORDER BY (
                    IF (recurringdate.recurringdate IS NULL,
                      e.starttime,
                      CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                    )
                ) ASC,
                event.title ASC';
        
        return $sql;
    }

    /**
     * Get the date and time for this day
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return new \DateTime('@'.mktime(0, 0, 0, $this->options['m'], $this->options['d'], $this->options['y']));
    }

    /**
     * Returns the permalink URL to this specific day.
     * 
     * @return string URL to this day.
     */
    public function getURL()
    {
        return self::generateURL($this->calendar, $this->getDateTime());
    }

    /**
     * Generate a Day URL for a specific calendar and date
     *
     * @param Calendar $calendar
     * @param \DateTime $datetime
     * @return string
     */
    public static function generateURL(Calendar $calendar, \DateTime $datetime)
    {
        return $calendar->getURL() . $datetime->format('Y/m/d') . '/';
    }

    /**
     * Get a relative day
     *
     * @param $string - +1, -1, etc
     * @return \UNL\UCBCN\Frontend\Month month
     */
    public function getRelativeDay($string)
    {
        $datetime = $this->getDateTime()->modify($string . ' day');

        $options = $this->options;
        $options['m'] = $datetime->format('m');
        $options['y'] = $datetime->format('Y');
        $options['d'] = $datetime->format('d');

        $class = get_called_class();

        return new $class($options);
    }

    /**
     * Get the previous month object
     *
     * @return \UNL\UCBCN\Frontend\Month month
     */
    public function getPreviousDay()
    {
        return $this->getRelativeDay('-1');
    }

    /**
     * Get the next month object
     *
     * @return \UNL\UCBCN\Frontend\Month month
     */
    public function getNextDay()
    {
        return $this->getRelativeDay('+1');
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
