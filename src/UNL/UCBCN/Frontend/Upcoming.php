<?php
/**
 * This class contains the information needed for viewing the list of upcoming
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
 * A list of upcoming events for a calendar.
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Upcoming extends EventListing implements RoutableInterface, MetaTagInterface
{
    /**
     * Calendar \UNL\UCBCN\Calendar Object
     *
     * @var \UNL\UCBCN\Calendar
     */
    public $calendar;
    private $isHomepage = false;

    public $options = array(
            'limit'  => 10,
            'offset' => 0,
    );

    /**
     * Constructs an upcoming event view for this calendar.
     *
     * @param array $options Associative array of options.
     */
    public function __construct($options = array())
    {
        // Set defaults
        $options['m'] = date('m');
        $options['d'] = date('d');
        $options['y'] = date('Y');
        $options['H'] = date('H');
        $options['i'] = date('i');
        $options['s'] = date('s');
        $options['includeEventImageData'] = TRUE;

        if (isset($options[0]) && !preg_match("/^\d{4}\/\d{1,2}\/\d{1,2}\/?/", $options[0])) {
            $this->isHomepage = true;
        }

        parent::__construct($options);
    }

    /**
     * Get the date and time for this day
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return new \DateTime('@'.
            mktime(
                $this->options['H'],
                $this->options['i'],
                $this->options['s'],
                $this->options['m'],
                $this->options['d'],
                $this->options['y']
            )
        );
    }

	/**
     * Get the SQL for finding events
     *
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    function getSQL()
    {
        $sql = '
                SELECT DISTINCT e.id as id, recurringdate.id as recurringdate_id
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (
                    recurringdate.event_datetime_id = e.id AND
                    recurringdate.unlinked = 0 AND
                    recurringdate.ongoing = 0
                )
                LEFT JOIN event_has_eventtype ON (event_has_eventtype.event_id = event.id)
                LEFT JOIN eventtype ON (eventtype.id = event_has_eventtype.eventtype_id)
                LEFT JOIN event_targets_audience ON (event_targets_audience.event_id = event.id)
                LEFT JOIN audience ON (audience.id = event_targets_audience.audience_id)
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND (
                        calendar_has_event.status =\'posted\'
                        OR calendar_has_event.status =\'archived\'
                    )
                    AND (
                        IF (recurringdate.recurringdate IS NULL,
                            e.starttime,
                            CONCAT(
                                DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),
                                DATE_FORMAT(e.starttime," %H:%i:%s")
                            )
                        ) >= NOW() OR
                        IF (recurringdate.recurringdate IS NULL,
                            e.endtime,
                            CONCAT(
                                DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),
                                DATE_FORMAT(e.endtime," %H:%i:%s")
                            )
                        ) >= NOW()
                    )
                ';

        // Adds filters for target audience
        if (!empty($this->event_type_filter)) {
            $sql .= 'AND ';
            $sql .= $this->getEventTypeSQL('eventtype');
        }

        // Adds filters for target audience
        if (!empty($this->audience_filter)) {
            $sql .= 'AND ';
            $sql .= $this->getAudienceSQL('audience');
        }

        // Adds filters for needs location
        if ($this->needs_location) {
            $sql .= 'AND ';
            $sql .= $this->getNeedsLocationSQL('e');
        }

        // Adds filters for Not TDB
        if ($this->not_TDB) {
            $sql .= 'AND ';
            $sql .= $this->getNotTDBSQL('e');
        }

        $sql .= '
                ORDER BY (
                        IF (recurringdate.recurringdate IS NULL,
                            e.starttime,
                            CONCAT(
                                DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),
                                DATE_FORMAT(e.starttime," %H:%i:%s")
                            )
                        )
                    ) ASC,
                    event.title ASC';
        $sql .= $this->setLimitClause($this->options['limit']);
        return $sql;
    }

    /**
     * Get a permanent URL to this object.
     *
     * @return string URL to this specific upcoming.
     */
    public function getURL()
    {
        $url_params = "";

        if (!empty($this->event_type_filter)) {
            $url_params .= $this->getEventTypeURLParam($url_params);
        }

        if (!empty($this->audience_filter)) {
            $url_params .= $this->getAudienceURLParam($url_params);
        }

        $url = $this->generateURL($this->calendar);

        return $url . $url_params;
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
            $title .= ' - Upcoming';
        }
        $description = 'The events calendar for ' . $this->calendar->name;

        $metaTagUtility = new MetaTagUtility($url, $title, $description);

        return $metaTagUtility->getMetaTags();
    }

    public function isHomepage()
    {
        return $this->isHomepage;
    }

    /**
     * Generate an Upcoming URL for a specific calendar
     *
     * @param Calendar $calendar
     * @return string
     */
    public static function generateURL(Calendar $calendar)
    {
        return $calendar->getURL() . 'upcoming/';
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

    protected function setLimitClause($limit) {
        if (is_numeric($limit) && $limit >= 1) {
            return ' LIMIT ' . (int)$limit;
        }
        return '';
    }
}
