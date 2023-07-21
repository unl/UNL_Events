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
class Day extends EventListing implements RoutableInterface, MetaTagInterface
{

    private $isHomepage = false;

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

        if (isset($options[0]) && !preg_match("/^\d{4}\/\d{1,2}\/\d{1,2}\/?/", $options[0])) {
            $this->isHomepage = true;
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
        // Due to timezones spanning multiple days open search up to all possible days.  Invalid events for day will be filtered out from results.
        $startDateTime = $this->getDateTime(FALSE, '-P2D')->format('Y-m-d H:i:s');
        $endDateTime = $this->getDateTime(TRUE, 'P2D')->format('Y-m-d H:i:s');

        $sql = '
                SELECT DISTINCT e.id as id,recurringdate.recurringdate,e.starttime,e.endtime,e.timezone,event.title, recurringdate.id as recurringdate_id
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (recurringdate.event_datetime_id = e.id AND recurringdate.recurringdate >= "' . $startDateTime . '" AND recurringdate.recurringdate <= "' . $endDateTime . '" AND recurringdate.unlinked = 0)
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND calendar_has_event.status IN ("posted", "archived")
                    AND (
                        (recurringdate.recurringdate IS NULL AND e.recurringtype = \'none\')
                        OR
                        (recurringdate.recurringdate IS NOT NULL AND e.recurringtype != \'none\')
                    )
                    AND  (
                        (e.endtime >= "' . $startDateTime . '" AND e.starttime <= "' . $endDateTime . '")
                       OR (recurringdate.recurringdate >= "' . $startDateTime . '" AND recurringdate.recurringdate <= "' . $endDateTime . '")
                      )
                ORDER BY (
                    IF (recurringdate.recurringdate IS NULL,
                      e.starttime,
                      CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                    )
                ) ASC,
                event.title ASC';

        return trim($sql);
    }

    // Overwrite to filter out bad results for timezone
    protected function getAllForConstructor()
    {
        if (array_key_exists('periodEvents', $this->options)) {
            $results = $this->options['periodEvents'];
        } else {
            $options['sql']         = $this->getSQL();
            $options['returnArray'] = true;
            $results = $this->getBySQL($options);
        }

        $filteredResults = array();
        $dayFilter = $this->getDateTime()->format('m-d-Y');
        $timezoneDisplay = \UNL\UCBCN::getTimezoneDisplay($this->calendar->defaulttimezone);
        foreach($results as $result) {

            if (!empty($result['recurringdate'])) {
                $startDateTime = $timezoneDisplay->getDateTime($result['recurringdate'] . substr($result['starttime'], -8), $result['timezone']);
                $endDateTime = $timezoneDisplay->getDateTime($result['recurringdate'] . substr($result['endtime'], -8), $result['timezone']);
            } else {
                $startDateTime = $timezoneDisplay->getDateTime($result['starttime'], $result['timezone']);
                $endDateTime = $timezoneDisplay->getDateTime($result['endtime'], $result['timezone']);
            }

            if ($this->isAllDayEvent($startDateTime,  $endDateTime)) {
                // Make endtime at end of day
                $endDateTime->add(new \DateInterval('PT23H59M59S'));
            } elseif ($startDateTime == $endDateTime) {
                // Bump endtime by 1 second so will show in period below
                $endDateTime->add(new \DateInterval('PT1S'));
            }

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($startDateTime, $interval, $endDateTime);

            foreach ($period as $eventDateTime) {
                if ($dayFilter == $eventDateTime->format('m-d-Y')) {
                    $filteredResults[] = $result;
                    break; // found day match so quit looking
                }
            }
        }

        return $filteredResults;
    }

    public function isHomepage()
    {
        return $this->isHomepage;
    }

    private function isAllDayEvent(\DateTime $startDateTime, \DateTime $endDateTime) {

        //It must start at midnight to be an all day event
        if (strpos($startDateTime->format('H:i:s'), '00:00:00') === false) {
            return false;
        }

        //It must end at midnight, or not have an end date.
        if (!empty($endDateTime) &&
            strpos($endDateTime->format('H:i:s'), '00:00:00') === false) {
            return false;
        }

        return true;
    }

    public function getDateTimeString($endOfDay = FALSE){
        if ($endOfDay === TRUE) {
            return $this->options['y'] . '-' . $this->options['m'] . '-' . $this->options['d'] . " 23:59:59";
        }
        return $this->options['y'] . '-' . $this->options['m'] . '-' . $this->options['d'] . " 00:00:00";
    }

    /**
     * Get the date and time for this day
     *
     * @return \DateTime
     */
    public function getDateTime($endOfDay = FALSE, $interval = NULL)
    {
        $timezoneDisplay = \UNL\UCBCN::getTimezoneDisplay($this->calendar->defaulttimezone);
        if (empty($interval)) {
            return $timezoneDisplay->getDateTime($this->getDateTimeString($endOfDay));
        } else {
            return $timezoneDisplay->getDateTimeAddInterval($this->getDateTimeString($endOfDay), $interval);
        }
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

    // Sets the meta tags for the page
    public function getMetaTags()
    {
        // Checks if it is the homepage
        $url = $this->getURL();
        if (!$this->isHomepage) {
            $url = $this->calendar->getURL();
        }

        $title = $this->calendar->name . ' Calendar';
        if (!$this->isHomepage) {
            $title .= ' - ' . $this->getDateTime()->format('F d, Y');
        }
        $description = 'The events calendar for ' . $this->calendar->name;

        $metaTagUtility = new MetaTagUtility($url, $title, $description);

        return $metaTagUtility->getMetaTags();
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
