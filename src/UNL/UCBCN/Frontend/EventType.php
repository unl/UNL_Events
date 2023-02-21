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
class EventType extends EventListing implements RoutableInterface
{
    public $search_query = '';
    public $search_event_audience = '';
    public $search_event_calendar = '';

    /**
     * Constructs this search output.
     *
     * @param array $options Associative array of options.
     * @throws UnexpectedValueException
     */
    public function __construct($options=array())
    {
        if (empty($options['q'])) {
            throw new UnexpectedValueException('Enter an event type to search for events.', 400);
        }
        
        $this->search_query = $options['q'] ?? "";
        $this->search_event_audience = $options['audience'] ?? "";
        $this->search_event_calendar = $options['calendar_id'] ?? "";
        
        parent::__construct($options);
    }

    /**
     * Get the SQL for finding events
     *
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    function getSQL()
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

        if (!empty($this->search_query)) {
            $eventtype_explode = explode(',', $this->search_query);
            $eventtype_explode = array_map('trim', $eventtype_explode);

            $sql .= ' AND (';
            foreach($eventtype_explode as $index=>$eventtype_single){
                if ($index > 0) {
                    $sql .= ' OR ';
                }
                $sql .= 'eventtype.name = \'' . self::escapeString($eventtype_single) . '\'';
            }
            $sql .= ') ';
        }

        if (!empty($this->search_event_audience)) {
            $sql .= ' AND ( audience.name = \'' . self::escapeString($this->search_event_audience) .'\')';
        }

        if (!empty($this->search_event_calendar)) {
            $sql .= ' AND ( calendar_has_event.calendar_id = \'' . (int)$this->search_event_calendar . '\') ';
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

    public function getFormattedEventTypes()
    {
        $output_string = '';
        $eventtype_explode = explode(',', $this->search_query);
        $eventtype_explode = array_map('trim', $eventtype_explode);
        $last_index = count($eventtype_explode) - 1;

        foreach($eventtype_explode as $index=>$eventtype_single){
            if ($index > 0 && $last_index >= 2) {
                $output_string .= ', ';
            }else if($index > 0){
                $output_string .= ' ';
            }
            if ($index === $last_index && $index > 0){
                $output_string .= 'and ';
            }
            $output_string .= $eventtype_single;
        }

        return $output_string;
    }

    /**
     * returns the url to this search page.
     *
     * @return string
     */
    public function getURL()
    {
        $url = '/eventtype/';
        
        if (!empty($this->search_query)) {
            $url .= '?q=' . urlencode($this->search_query);
        }

        return $url;
    }
    
}
