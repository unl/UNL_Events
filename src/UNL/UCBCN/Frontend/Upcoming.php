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
        $sql = 'SELECT 
                    DISTINCT e.id as id,
                    rd.id as recurringdate_id
                FROM eventdatetime as e
                LEFT JOIN recurringdate as rd ON (
                    e.recurringtype != "none" AND
                    rd.event_datetime_id = e.id AND
                    rd.unlinked = 0 AND
                    rd.ongoing = 0
                )
                JOIN event ON
                    e.event_id = event.id
                WHERE 
                    (
                        COALESCE(TIMESTAMP(rd.recurringdate, TIME(e.starttime)), e.starttime) >= NOW() OR
                        COALESCE(TIMESTAMP(rd.recurringdate, TIME(e.endtime)), e.endtime) >= NOW()
                    )
                    AND
                    EXISTS (
                        SELECT * FROM calendar_has_event
                        WHERE
                            calendar_has_event.calendar_id = ' . (int)$this->calendar->id . ' AND
                            calendar_has_event.event_id = e.event_id AND
                            calendar_has_event.status IN ("posted", "archived")
                    )';

        // Adds filters for target audience
        if (!empty($this->event_type_filter)) {
            $sql .= 'AND
                EXISTS (
                    SELECT *
                        FROM event_has_eventtype
                    INNER JOIN eventtype
                        ON (eventtype.id = event_has_eventtype.eventtype_id)
                        AND (eventtype.name = "' . $this->event_type_filter . '")
                    WHERE
                        event_has_eventtype.event_id = e.event_id
                )
            ';
        }

        // Adds filters for target audience
        if (!empty($this->audience_filter)) {
            $sql .= 'AND
                EXISTS (
                    SELECT *
                        FROM event_targets_audience
                    INNER JOIN audience
                        ON (audience.id = event_targets_audience.audience_id)
                        AND (audience.name = "' . $this->audience_filter . '")
                    WHERE
                        event_targets_audience.event_id = e.event_id
                )
            ';
        }

        // Adds filters for target audience
        if (!empty($this->audience_filter)) {
            $sql .= 'AND
                EXISTS (
                    SELECT *
                        FROM event_targets_audience
                    INNER JOIN audience
                        ON (audience.id = event_targets_audience.audience_id)
                        AND (audience.name = "' . $this->audience_filter . '")
                    WHERE
                        event_targets_audience.event_id = e.event_id
                )
            ';
        }

        // Adds filters for needs location
        if ($this->needs_location) {
            $sql .= 'AND ';
            $sql .= $this->getNeedsLocationSQL('e');
        }

        // Adds filters for time mode
        if ($this->time_mode_filter) {
            $sql .= 'AND ';
            $sql .= $this->getTimeModeSQL('e');
        }

        $sql .= '
        ORDER BY
            COALESCE(TIMESTAMP(rd.recurringdate, TIME(e.starttime)), e.starttime) ASC,
            event.title ASC
        ';

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
