<?php
/**
 * This class contains the information needed for viewing the list of featured
 * events within the calendar system.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @version   CVS: $id$
 * @link      http://code.google.com/p/unl-event-publisher/
 */
namespace UNL\UCBCN\Frontend;

/**
 * A list of current featured events for a calendar.
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 */
class Featured extends EventListing implements RoutableInterface
{
    /**
     * Calendar \UNL\UCBCN\Calendar Object
     *
     * @var \UNL\UCBCN\Calendar
     */
    public $calendar;

    public $options = array(
        'pinned_limit' => 1,
        'limit'  => 10,
        'offset' => 0,
    );

    /**
     * Constructs an featured event view for this calendar.
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

        parent::__construct($options);
    }

    /**
     * Get the date and time for this day
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return new \DateTime('@'.mktime($this->options['H'], $this->options['i'], $this->options['s'], $this->options['m'], $this->options['d'], $this->options['y']));
    }

    /**
     * Get the SQL for finding events
     *
     * @see \UNL\UCBCN\ActiveRecord\RecordList::getSQL()
     */
    function getSQL()
    {
        $timestamp = $this->getDateTime()->getTimestamp();
        $eventFilters = array();

        // Want to always include the pinned limit of pinned events
        $pinnedResults = $this->getPinnedEvents($timestamp);
        foreach ($pinnedResults as $result) {
            $filter = '(event.id = ' . $result['eventID'] . ' AND e.id = ' . $result['eventDatetimeID'];
            if (!empty($result['recurringDateID'])) {
                $filter .= ' AND recurringdate.id = ' . $result['recurringDateID'];
            }
            $filter .= ')';
            $eventFilters[] = $filter;
        }

        // Want to include the limit of Featured Events including pinned events within the limit
        $featuredResults = $this->getFeaturedEvents($timestamp);
        foreach ($featuredResults as $result) {
            if ($this->options['limit'] > 0 && count($eventFilters) < $this->options['limit']) {
                $filter = '(event.id = ' . $result['eventID'] . ' AND e.id = ' . $result['eventDatetimeID'];
                if (!empty($result['recurringDateID'])) {
                    $filter .= ' AND recurringdate.id = ' . $result['recurringDateID'];
                }
                $filter .= ')';
                $eventFilters[] = $filter;
            }
        }

        $eventFilterString = implode(' OR ', $eventFilters);
        if (empty($eventFilterString)) {
            $eventFilterString = '1 <> 1';
        }

        $sql = $this->setFeaturedSelect('(' . $eventFilterString . ')', $timestamp, TRUE);
        $sql .= $this->setLimitClause($this->options['limit']);

        return trim($sql);
    }

    private function getFeaturedEvents($timestamp) {
        $sql = $this->setFeaturedSelect('calendar_has_event.featured = 1', $timestamp, FALSE);
        $sql .= $this->setLimitClause($this->options['limit']);

        $options['sql']         = $sql;
        $options['returnArray'] = true;
        return self::getBySQL($options);
    }

    private function getPinnedEvents($timestamp) {
        $sql = $this->setFeaturedSelect('calendar_has_event.pinned = 1', $timestamp, FALSE);
        $sql .= $this->setLimitClause($this->options['pinned_limit']);
        $options['sql']         = $sql;
        $options['returnArray'] = true;
        return self::getBySQL($options);
    }

    private function setFeaturedSelect($specialFilter, $timestamp, $useFinalColumns = TRUE) {
        $columns = 'event.id as eventID, e.id as eventDatetimeID, recurringdate.id as recurringDateID';
        if ($useFinalColumns) {
            $columns = 'e.id as id, recurringdate.id as recurringdate_id';
        }

        return 'SELECT ' . $columns . '
                FROM eventdatetime as e
                INNER JOIN event ON e.event_id = event.id
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = event.id
                LEFT JOIN recurringdate ON (recurringdate.event_datetime_id = e.id AND recurringdate.unlinked = 0)
                WHERE calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND ' . $specialFilter . '
                    AND (
                        calendar_has_event.status =\'posted\'
                        OR calendar_has_event.status =\'archived\'
                    )
                    AND (
                        IF (recurringdate.recurringdate IS NULL,
                            e.starttime,
                            recurringdate.recurringdate
                        ) >=  "'.date('Y-m-d', $timestamp).'"
                    )
                ORDER BY (
                    IF (recurringdate.recurringdate IS NULL,
                       e.starttime,
                       CONCAT(DATE_FORMAT(recurringdate.recurringdate,"%Y-%m-%d"),DATE_FORMAT(e.starttime," %H:%i:%s"))
                    )
                ) ASC, event.title ASC';
    }

    private function setLimitClause($limit) {
        if (is_numeric($limit) && $limit >= 1) {
            return ' LIMIT ' . (int)$limit;
        }
        return '';
    }

    /**
     * Get a permanent URL to this object.
     *
     * @return string URL to this specific featured.
     */
    public function getURL()
    {
        return $this->generateURL($this->calendar);
    }

    /**
     * Generate an Featured URL for a specific calendar
     *
     * @param Calendar $calendar
     * @return string
     */
    public static function generateURL(Calendar $calendar)
    {
        return $calendar->getURL() . 'featured/';
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
