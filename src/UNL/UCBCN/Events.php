<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;
/**
 * Object related to a list of events.
 * 
 * PHP version 5
 * 
 * @category  Events 
 * @package   UNL_UCBCN
 * @author    Tyler Lemburg <trlemburg@gmail.com>
 * @copyright 2015 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

class Events extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Event',
        );
    }

    public function getSQL() {
        if (array_key_exists('calendar', $this->options)) {
            # get all events related to the calendar through a join on calendar has event and calendar.
            $sql = '
                SELECT event.id FROM event 
                INNER JOIN calendar_has_event ON event.id = calendar_has_event.event_id
                INNER JOIN calendar ON calendar_has_event.calendar_id = calendar.id
                WHERE calendar.shortname = "' . self::escapeString($this->options['calendar']) . '";';
            return $sql;
        } else {
            return parent::getSQL();
        }
    }
}
