<?php
/**
 * Event type search class for frontend users to search for events for all calendars
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @version   CVS: $id$
 * @link      http://code.google.com/p/unl-event-publisher/
 */
namespace UNL\UCBCN\Frontend;

/**
 * Container for event type search results for the frontend.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class EventType extends EventListing implements RoutableInterface, MetaTagInterface
{
    public $search_event_calendar = '';
    public $limit = 100;
    public $offset = 0;
    public $max_limit = array(
        'json' => 500,
        'xml' => 500,
        'default' => 100
    );

    /**
     * Constructs this search output.
     *
     * @param array $options Associative array of options.
     * @throws UnexpectedValueException
     */
    public function __construct($options=array())
    {
        // If calendar id is provided we will add that to the query
        $this->search_event_calendar = $options['calendar_id'] ?? "";

        // backwards compatibility
        if (empty($options['type']) && !empty($options['q'])) {
            $options['type'] = $options['q'];
        }

        $format_max_limit = $this->max_limit['default'];
        if (array_key_exists($options['format'], $this->max_limit)) {
            $format_max_limit = $this->max_limit[$options['format']];
        }

        if (!isset($options['limit']) ||
            empty($options['limit']) ||
            intval($options['limit']) > $format_max_limit ||
            intval($options['limit']) <= 0
        ) {
            $options['limit'] = $this->max_limit['default'];
        }

        if (!isset($options['offset']) || empty($options['offset']) ||  intval($options['offset']) <= 0) {
            $options['offset'] = 0;
        }

        $this->limit = $options['limit'] ?? $this->limit;
        $this->offset = $options['offset'] ?? $this->offset;

        parent::__construct($options);
    }

    /**
     * Get the SQL for finding events
     *
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    public function getSQL()
    {
        // Sets up calendar id for filters
        $calendar_id = (int)$this->calendar->id;
        if (!empty($this->search_event_calendar)) {
            $calendar_id = (int)$this->search_event_calendar;
        }

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
                WHERE
                    (
                        COALESCE(TIMESTAMP(rd.recurringdate, TIME(e.starttime)), e.starttime) >= NOW() OR
                        COALESCE(TIMESTAMP(rd.recurringdate, TIME(e.endtime)), e.endtime) >= NOW()
                    )
                    AND
                    (
                        EXISTS (
                            SELECT * FROM calendar_has_event
                            WHERE
                                calendar_has_event.calendar_id = ' . $calendar_id. ' AND
                                calendar_has_event.event_id = e.event_id AND
                                calendar_has_event.status IN ("posted", "archived")
                        )
                        OR
                        EXISTS (
                            SELECT * FROM event
                            WHERE
                                event.id = e.event_id AND
                                event.approvedforcirculation = 1
                        )
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

        $sql .= '
        ORDER BY
            COALESCE(TIMESTAMP(rd.recurringdate, TIME(e.starttime)), e.starttime) ASC,
            (SELECT title FROM event WHERE event.id = e.event_id) ASC
        ';

        return $sql;
    }

    // Sets the meta tags for the page
    public function getMetaTags()
    {
        $title = $this->calendar->name . ' Calendar - Event Type';
        $description = 'The UNL events calendar for ' . $this->calendar->name;

        $metaTagUtility = new MetaTagUtility($this->getURL(), $title, $description);

        return $metaTagUtility->getMetaTags();
    }

    /**
     * returns the url to this eventtype page.
     *
     * @return string
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

        $url = self::generateURL($this->calendar);

        return $url . $url_params;
    }

    public static function generateURL(Calendar $calendar)
    {
        return $calendar->getURL() . 'eventtype/';
    }
}
