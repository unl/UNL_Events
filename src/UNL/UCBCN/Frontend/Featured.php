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
class Featured extends Upcoming
{
    /**
     * Constructs an featured event view for this calendar.
     *
     * @param array $options Associative array of options.
    */
    public function __construct($options = array())
    {
        // Set defaults
        $options['pinned_limit'] = 1;

        parent::__construct($options);
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
        $pinnedEventIDs = array();
        foreach ($pinnedResults as $result) {
            $pinnedEventIDs[] = $result;
            $filter = '(e.event_id = ' . $result['eventID'] . ' AND e.id = ' . $result['eventDatetimeID'];
            if (!empty($result['recurringDateID'])) {
                $filter .= ' AND rd.id = ' . $result['recurringDateID'];
            }
            $filter .= ')';
            $eventFilters[] = $filter;
        }

        // Want to include the limit of Featured Events including pinned events within the limit
        $featuredResults = $this->getFeaturedEvents($timestamp, count($pinnedResults));
        foreach ($featuredResults as $result) {
            if (!$this->isPinnedEventResult($result, $pinnedResults) && $this->options['limit'] > 0 && count($eventFilters) < $this->options['limit']) {
                $filter = '(e.event_id = ' . $result['eventID'] . ' AND e.id = ' . $result['eventDatetimeID'];
                if (!empty($result['recurringDateID'])) {
                    $filter .= ' AND rd.id = ' . $result['recurringDateID'];
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

    private function isPinnedEventResult($result, $pinnedResults) {
        foreach ($pinnedResults as $pinnedResult) {
            if ($result['eventID'] !== $pinnedResult['eventID']) {
                continue;
            }
            if (!empty($result['recurringDateID']) && !empty($pinnedResult['recurringDateID']) && $result['recurringDateID'] !== $pinnedResult['recurringDateID']) {
                continue;
            }
            if ($result['eventID'] === $pinnedResult['eventID']) {
                return true;
            }
        }
        return false;
    }

    private function getFeaturedEvents($timestamp, $limitAdjustment = 0) {
        $sql = $this->setFeaturedSelect('calendar_has_event.featured = 1', $timestamp, FALSE);
        $sql .= $this->setLimitClause($this->options['limit'] + $limitAdjustment);

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
        $columns = 'e.event_id as eventID, e.id as eventDatetimeID, rd.id as recurringDateID';
        if ($useFinalColumns) {
            $columns = 'e.id as id, rd.id as recurringdate_id';
        }

        return 'SELECT ' . $columns . '
                    FROM eventdatetime as e
                    LEFT JOIN recurringdate as rd ON (
                    e.recurringtype != "none"
                    AND rd.event_datetime_id = e.id
                    AND rd.unlinked = 0
                    AND rd.ongoing = 0
                )
                INNER JOIN calendar_has_event ON calendar_has_event.event_id = e.event_id
                WHERE calendar_has_event.calendar_id = ' . (int)$this->calendar->id . '
                    AND ' . $specialFilter . '
                    AND (
                        calendar_has_event.status =\'posted\'
                        OR calendar_has_event.status =\'archived\'
                    )
                    AND (
                        (rd.recurringdate IS NULL AND e.recurringtype = \'none\')
                        OR
                        (rd.recurringdate IS NOT NULL AND e.recurringtype != \'none\')
                    )
                    AND (
                        COALESCE(
                            rd.recurringdate,
                            e.starttime
                        ) >= "'.date('Y-m-d', $timestamp).'"
                    )
                ORDER BY COALESCE(
                        TIMESTAMP(rd.recurringdate, TIME(e.starttime)),
                        e.starttime
                    ) ASC, (
                    SELECT title
                        FROM event
                        WHERE event.id = e.event_id
                    ) ASC';
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
}
