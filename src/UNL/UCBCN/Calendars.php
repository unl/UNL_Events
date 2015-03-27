<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;
/**
 * Object related to a list of calendars.
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

class Calendars extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Calendar',
        );
    }

    public function getSQL() {
        if (array_key_exists('account_id', $this->options)) {
            # get all events related to the calendar through a join on calendar has event and calendar.
            $sql = '
                SELECT calendar.id FROM calendar
                WHERE calendar.account_id = ' . self::escapeString($this->options['account_id']) . ';';
            return $sql;
        } else {
            return parent::getSQL();
        }
    }
}
