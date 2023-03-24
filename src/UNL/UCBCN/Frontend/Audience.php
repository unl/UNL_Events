<?php
/**
 * Audience search class for frontend users to search for events from all calendars
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

use UNL\UCBCN\Calendar\Audiences;

/**
 * Container for audience search results for the frontend.
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
class Audience extends EventListing implements RoutableInterface
{
    public $search_query = '';
    public $search_event_type = '';
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
        if (empty($options['q'])) {
            throw new UnexpectedValueException('Enter an audience to search for events.', 400);
        }

        // List of audiences (Comma separated)
        $this->search_query = $options['q'] ?? "";

        $this->search_event_type = $options['type'] ?? "";
        $this->search_event_calendar = $options['calendar_id'] ?? "";

        $format_max_limit = $this->max_limit['default'];
        if (array_key_exists($options['format'], $this->max_limit)) {
            $format_max_limit = $this->max_limit[$options['format']];
        }

        if (!isset($options['limit']) ||
            empty($options['limit']) ||
            intval($options['limit']) > $format_max_limit ||
            intval($options['limit']) <= 0
        ) {
            $options['limit'] = $format_max_limit;
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
    protected function getSQL()
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
                WHERE calendar_has_event.status IN ("posted", "archived") AND
                (
                    IF (recurringdate.recurringdate IS NULL,
                        e.starttime,
                        CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                    ) >= NOW() OR
                    IF (recurringdate.recurringdate IS NULL,
                        e.endtime,
                        CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.endtime," %H:%i:%s"))
                    ) >= NOW()
                )';

        // splits the audiences by comma and creates the SQL for those
        if (!empty($this->search_query)) {
            $audiences_explode = $this->getSplitAudiences();

            $sql .= ' AND (';
            foreach ($audiences_explode as $index => $audience_single) {
                if ($index > 0) {
                    $sql .= ' OR ';
                }
                $sql .= 'audience.name = \'' . self::escapeString($audience_single) . '\'';
            }
            $sql .= ') ';
        }

        // Adds any filters for event type
        if (!empty($this->search_event_type)) {
            $sql .= ' AND ( eventtype.name = \'' . self::escapeString($this->search_event_type) .'\')';
        }

        // Adds any filters for calendar id
        if (!empty($this->search_event_calendar)) {
            $sql .= ' AND ( calendar_has_event.calendar_id = \'' . (int)$this->search_event_calendar . '\') ';
        }

        // Adds the remaining sql
        $sql .= 'ORDER BY (
                        IF (recurringdate.recurringdate IS NULL,
                            e.starttime,
                            CONCAT(
                                DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),
                                DATE_FORMAT(e.starttime," %H:%i:%s")
                            )
                        )
                    ) ASC,
                    event.title ASC';

        return $sql;
    }

    /**
     * Splits the search query by commas and trims whitespace from all the items
     *
     * @return string[]
     */
    public function getSplitAudiences(): array
    {
        if (empty($this->search_query)) {
            return array();
        }

        // splits the audiences by comma
        $audiences_explode = explode(',', $this->search_query);
        $audiences_explode = array_map('trim', $audiences_explode);

        return $audiences_explode;
    }

    /**
     * Returns the count of the items in the query
     * This is only here becuase savvy will mess up the array
     *
     * @return int
     */
    public function countQuery():int
    {
        return count($this->getSplitAudiences());
    }


    /**
     * returns nicely formatted string of the audiences from the search query
     *
     * @return string
     */
    public function getFormattedAudiences()
    {
        $output_string = '';
        $audiences_explode = $this->getSplitAudiences();
        $last_index = count($audiences_explode) - 1;

        foreach ($audiences_explode as $index => $audience_single) {
            if ($index > 0 && $last_index >= 2) {
                $output_string .= ', ';
            } elseif ($index > 0) {
                $output_string .= ' ';
            }
            if ($index === $last_index && $index > 0) {
                $output_string .= 'and ';
            }
            $output_string .= ucwords($audience_single);
        }

        return $output_string;
    }

    /**
     * returns the url to this audience page.
     *
     * @return string
     */
    public function getURL()
    {
        $url = '/audience/';

        if (!empty($this->search_query)) {
            $url .= '?q=' . urlencode($this->search_query);
        }

        return $url;
    }

    /**
     * Gets list of all audiences
     *
     * @return bool|Audiences - false if no audiences, otherwise return recordList of all audiences
     */
    public function getAudiences()
    {
        return new Audiences(array('order_name' => true));
    }

}
