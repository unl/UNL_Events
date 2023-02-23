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
    public $search_limit = 100;
    public $search_offset = 0;

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

        $this->search_limit = $options['q_limit'] ?? 100;
        $this->search_offset = $options['q_offset'] ?? 0;

        if (intval($this->search_limit) > 100) { $this->search_limit = 100; }
        if (intval($this->search_limit) <= 0)  { $this->search_limit = 1; }
        if (intval($this->search_offset) < 0) { $this->search_offset = 0; }

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
                        e.starttime>=\''. date('Y-m-d') .' 00:00:00\' OR
                        e.endtime>\''. date('Y-m-d') .' 00:00:00\'
                    )
                ';

        // splits the audiences by comma and creates the SQL for those
        if (!empty($this->search_query)) {
            $audiences_explode = explode(',', $this->search_query);
            $audiences_explode = array_map('trim', $audiences_explode);

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

        $sql .= " limit " . (int) $this->search_limit;
        $sql .= " offset " . (int) $this->search_offset;

        return $sql;
    }

    /**
     * returns nicely formatted string of the audiences from the search query
     *
     * @return string
     */
    public function getFormattedAudiences()
    {
        $output_string = '';
        $audiences_explode = explode(',', $this->search_query);
        $audiences_explode = array_map('trim', $audiences_explode);
        $last_index = count($audiences_explode) - 1;

        foreach ($audiences_explode as $index => $audience_single) {
            if ($index > 0 && $last_index >= 2) {
                $output_string .= ', ';
            } else if($index > 0) {
                $output_string .= ' ';
            }
            if ($index === $last_index && $index > 0) {
                $output_string .= 'and ';
            }
            $output_string .= $audience_single;
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
}
