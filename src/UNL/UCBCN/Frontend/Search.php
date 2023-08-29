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
        $sql = 'SELECT DISTINCT e.id as id, recurringdate.id as recurringdate_id
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (recurringdate.event_datetime_id = e.id AND recurringdate.unlinked = 0)
                LEFT JOIN event_has_eventtype ON (event_has_eventtype.event_id = event.id)
                LEFT JOIN eventtype ON (eventtype.id = event_has_eventtype.eventtype_id)
                LEFT JOIN event_targets_audience ON (event_targets_audience.event_id = event.id)
                LEFT JOIN audience ON (audience.id = event_targets_audience.audience_id)
                LEFT JOIN location ON (location.id = e.location_id)
                LEFT JOIN webcast ON (webcast.id = e.webcast_id)
                WHERE
                    calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND calendar_has_event.status IN ("posted", "archived")';

        if ($this->date_parser->parsed) {
            if ($this->date_parser->single) {
                $sql .= ' AND (
                    IF (recurringdate.recurringdate IS NULL,
                        DATE_FORMAT(e.starttime,"%Y-%m-%d"),
                        recurringdate.recurringdate
                    ) = STR_TO_DATE(\'' . date('Y-m-d', $this->date_parser->start_date) . '\', \'%Y-%m-%d\')
                )';
            } else {
                $sql .= 'AND (
                    IF (recurringdate.recurringdate IS NULL,
                        DATE_FORMAT(e.starttime,"%Y-%m-%d"),
                        recurringdate.recurringdate
                    ) >= STR_TO_DATE(\'' . date('Y-m-d', $this->date_parser->start_date) . '\', \'%Y-%m-%d\')
                    AND
                    IF (recurringdate.recurringdate IS NULL,
                        DATE_FORMAT(e.starttime,"%Y-%m-%d"),
                        recurringdate.recurringdate
                    ) <= STR_TO_DATE(\'' . date('Y-m-d', $this->date_parser->end_date) . '\', \'%Y-%m-%d\')
                )';
            }
        } else {
            if (!empty($this->search_query)) {
                // Do a textual search.
                $sql .= 'AND (
                    (event.title LIKE \'%'.self::escapeString($this->search_query).'%\') OR
                    (eventtype.name LIKE \'%'.self::escapeString($this->search_query).'%\') OR
                    (audience.name LIKE \'%'.self::escapeString($this->search_query).'%\') OR
                    (event.description LIKE \'%'.self::escapeString($this->search_query).'%\') OR
                    (location.name LIKE \'%'.self::escapeString($this->search_query).'%\') OR
                    (webcast.title LIKE \'%'.self::escapeString($this->search_query).'%\')
                )';
            }

            $sql .= 'AND (IF (recurringdate.recurringdate IS NULL,
                e.starttime,
                CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
            ) >= NOW() OR
            IF (recurringdate.recurringdate IS NULL,
                e.endtime,
                CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.endtime," %H:%i:%s"))
            ) >= NOW())';
        }

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

        $sql .= 'ORDER BY (
                        IF (recurringdate.recurringdate IS NULL,
                          e.starttime,
                          CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                        )
                    ) ASC,
                    event.title ASC';

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
