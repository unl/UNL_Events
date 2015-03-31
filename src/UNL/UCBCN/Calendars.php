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
        if (array_key_exists('user_id', $this->options)) {
            # get all calendars related through a join on user_has_permission
            $sql = '
                SELECT DISTINCT calendar.id FROM calendar
                INNER JOIN user_has_permission ON calendar.id = user_has_permission.calendar_id
                INNER JOIN user ON user_has_permission.user_uid = user.uid
                WHERE user.uid = "' . self::escapeString($this->options['user_id']) . '";';
            return $sql;
        } else {
            return parent::getSQL();
        }
    }
}
