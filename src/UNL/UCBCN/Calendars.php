<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;

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
            return '
                SELECT DISTINCT id FROM (SELECT calendar.id, calendar.name FROM calendar
                INNER JOIN user_has_permission ON calendar.id = user_has_permission.calendar_id
                INNER JOIN user ON user_has_permission.user_uid = user.uid
                WHERE user.uid = "' .
                self::escapeString($this->options['user_id']) .
                '") as distinctfilter ORDER BY name';
        } elseif (array_key_exists('subscription_id', $this->options)) {
            # get all calendars that are subscribed with a certain subscription
            return '
                SELECT calendar_id FROM subscription_has_calendar
                WHERE subscription_id = ' . (int)$this->options['subscription_id'] . ';';
        } elseif (array_key_exists('recommendable_within_account_id', $this->options)) {
            # get all calendars that allow recommendations within the given account
            return '
                SELECT id FROM calendar
                WHERE account_id = ' .
                (int)$this->options['recommendable_within_account_id'] .
                ' AND recommendationswithinaccount = 1;';
        } elseif (array_key_exists('recommend_permissions_for_user_uid', $this->options)) {
            # get all calendars where the user has event post or event send to pending permissions
            return '
                SELECT DISTINCT calendar.id FROM calendar
                INNER JOIN user_has_permission ON calendar.id = user_has_permission.calendar_id
                INNER JOIN permission ON user_has_permission.permission_id = permission.id
                WHERE (permission.name = "Event Post" OR permission.name = "Event Send Event to Pending Queue")
                AND user_has_permission.user_uid = "' .
                self::escapeString($this->options['recommend_permissions_for_user_uid']) .
                '";';
        } elseif (array_key_exists('original_calendars_for_event_id', $this->options)) {
            return 'SELECT DISTINCT calendar_id FROM calendar_has_event
                    WHERE (source = "' .
                    CalendarHasEvent::SOURCE_CREATE_EVENT_FORM .
                    '" OR source = "' . CalendarHasEvent::SOURCE_CHECKED_CONSIDER_EVENT . '")
                    AND event_id = ' . (int)$this->options['original_calendars_for_event_id'] . ';';
        } elseif (array_key_exists('has_event_activity_since', $this->options)) {
            $format = 'Y-m-d';
            $date = $this->options['has_event_activity_since'];
            $d = \DateTime::createFromFormat($format, $date);
            // use date passed if valid and in expected format or use default date of a year ago
            $filter_date = $d && $d->format($format) === $date ? $date : date($format, strtotime('-1 Year'));
            $sql = "
                SELECT DISTINCT calendar.* FROM calendar
                INNER JOIN calendar_has_event ON calendar.id = calendar_has_event.calendar_id
                WHERE calendar_has_event.datelastupdated >= '" . $filter_date . "'
                ORDER BY calendar.name";
            return trim($sql);
        } elseif (array_key_exists('search_query', $this->options)) {
            return '
                SELECT calendar.id
                FROM calendar
                LEFT OUTER JOIN user on user.uid = calendar.shortname
                WHERE (calendar.shortname LIKE "%' . self::escapeString($this->options['search_query']) . '%"
                OR calendar.name LIKE "%' . self::escapeString($this->options['search_query']) . '%")
                AND (user.uid is null OR user.uid = "' . self::escapeString($this->options['search_query']) . '")
                ORDER BY id;
            ';
        } else {
            return parent::getSQL();
        }
    }
}
