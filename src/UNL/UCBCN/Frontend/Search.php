<?php
/**
 * Search class for frontend users to search for events.
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
 * @todo      Add searching by eventtype.
 */
namespace UNL\UCBCN\Frontend;

use UNL\UCBCN\Frontend\DateStringParser;
use UNL\UCBCN\Event;

/**
 * Container for search results for the frontend.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Search extends EventListing implements RoutableInterface, MetaTagInterface
{
    public $search_query = '';

    public $limit = 100;
    public $offset = 0;
    public $max_limit = array(
        'json' => 500,
        'xml' => 500,
        'default' => 100
    );

    /**
     * Calendar \UNL\UCBCN\Calendar Object
     *
     * @var \UNL\UCBCN\DateStringParser
     */
    private $date_parser;

    /**
     * Constructs this search output.
     *
     * @param array $options Associative array of options.
     * @throws UnexpectedValueException
     */
    public function __construct($options=array())
    {

        // Removed error for when search query is empty because I think it would be useful
        // There might be a better way to remove % from searches
        $this->search_query = str_replace('%', '', $options['q'] ?? "");

        $format_max_limit = $this->max_limit['default'];
        if (key_exists('format', $options) && array_key_exists($options['format'], $this->max_limit)) {
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

        $this->date_parser = new DateStringParser($this->search_query);

        parent::__construct($options);
    }

    /**
     * Get the SQL for finding events
     *
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    public function getSQL()
    {

        $sql = 'SELECT DISTINCT e.id as id,
            rd.id as recurringdate_id
        FROM eventdatetime as e
            LEFT JOIN recurringdate as rd ON (
                e.recurringtype != "none"
                AND rd.event_datetime_id = e.id
                AND rd.unlinked = 0
                AND rd.ongoing = 0
            )
        WHERE
            EXISTS (
                SELECT * FROM calendar_has_event
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . ' AND
                    calendar_has_event.event_id = e.event_id AND
                    calendar_has_event.status IN ("posted", "archived")
            )';

        // Add date filtering for non-recurring events
        if ($this->date_parser->parsed) {
            if ($this->date_parser->single) {
                // Single Date
                $sql .= ' AND
                    COALESCE(
                        rd.recurringdate,
                        e.starttime
                    ) = STR_TO_DATE(\'' . date('Y-m-d', $this->date_parser->start_date) . '\', \'%Y-%m-%d\')
                ';
            } else {
                // Add NOW() filter after text search
                $sql .= ' AND
                    COALESCE(
                        TIMESTAMP(rd.recurringdate, TIME(e.starttime)),
                        e.starttime
                    ) >= STR_TO_DATE(\'' . date('Y-m-d', $this->date_parser->start_date) . '\', \'%Y-%m-%d\')
                    AND COALESCE(
                        TIMESTAMP(rd.recurringdate, TIME(e.starttime)),
                        e.starttime
                    ) <= STR_TO_DATE(\'' . date('Y-m-d', $this->date_parser->end_date) . '\', \'%Y-%m-%d\')
                ';
            }
        } else {
            // Text search

            $sql .= 'AND (
                EXISTS (
                    SELECT *
                        FROM event
                    WHERE
                        event.id = e.event_id AND (
                            event.title LIKE \'%'.self::escapeString($this->search_query).'%\'
                            OR
                            event.description LIKE \'%'.self::escapeString($this->search_query).'%\'
                        )
                )
                OR
                EXISTS (
                    SELECT *
                        FROM event_has_eventtype
                    INNER JOIN eventtype
                        ON (eventtype.id = event_has_eventtype.eventtype_id)
                        AND (eventtype.name LIKE \'%'.self::escapeString($this->search_query).'%\')
                    WHERE
                        event_has_eventtype.event_id = e.event_id
                )
                OR
                EXISTS (
                    SELECT *
                        FROM event_targets_audience
                    INNER JOIN audience
                        ON (audience.id = event_targets_audience.audience_id)
                        AND (audience.name LIKE \'%'.self::escapeString($this->search_query).'%\')
                    WHERE
                        event_targets_audience.event_id = e.event_id
                )
                OR
                EXISTS (
                    SELECT *
                        FROM location
                    WHERE
                        location.id = e.location_id
                        AND (location.name LIKE \'%'.self::escapeString($this->search_query).'%\')
                )
                OR
                EXISTS (
                    SELECT *
                        FROM webcast
                    WHERE
                        webcast.id = e.webcast_id
                        AND (webcast.title LIKE \'%'.self::escapeString($this->search_query).'%\')
                )
            )';

        // Add NOW() filter after text search
        $sql .= ' AND (
                COALESCE(
                    TIMESTAMP(rd.recurringdate, TIME(e.starttime)),
                    e.starttime
                ) >= NOW()
                OR COALESCE(
                    TIMESTAMP(rd.recurringdate, TIME(e.endtime)),
                    e.endtime
                ) >= NOW()
            )';
        }


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

    /**
     * Returns bool for if the parser found a range
     *
     * @return bool
     */
    public function isDateRange():bool
    {
        return $this->date_parser->parsed && !$this->date_parser->single;
    }

    /**
     * Returns bool for if the parser found a single date
     *
     * @return bool
     */
    public function isSingleDate():bool
    {
        return $this->date_parser->parsed && $this->date_parser->single;
    }

    /**
     * Returns bool if no date found or string of unix timestamp
     *
     * @return int|bool
     */
    public function getStartDate()
    {
        return $this->date_parser->start_date;
    }

    /**
     * Returns bool if no date range found or string of unix timestamp
     *
     * @return int|bool
     */
    public function getEndDate()
    {
        return $this->date_parser->end_date;
    }

    // Sets the meta tags for the page
    public function getMetaTags()
    {
        $title = $this->calendar->name . ' Calendar - Search';
        $description = 'The UNL events calendar for ' . $this->calendar->name;

        $metaTagUtility = new MetaTagUtility($this->getURL(), $title, $description);

        return $metaTagUtility->getMetaTags();
    }

    /**
     * returns the url to this search page.
     *
     * @return string
     */
    public function getURL()
    {
        $url_params = "";

        if (isset($this->search_query)) {
            $url_params .= '?q=' . urlencode($this->search_query);
        }

        if (!empty($this->event_type_filter)) {
            $url_params .= $this->getEventTypeURLParam($url_params);
        }

        if (!empty($this->audience_filter)) {
            $url_params .= $this->getAudienceURLParam($url_params);
        }

        $url = $this->options['calendar']->getURL() . 'search/';

        return $url . $url_params;
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
