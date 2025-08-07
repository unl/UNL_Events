<?php
namespace UNL\UCBCN;
use UNL\UCBCN\ActiveRecord\RecordList;

class Users extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\User',
        );
    }

    function __construct($options = array())
    {
        parent::__construct($options);
    }

    function getSQL()
    {
        if (array_key_exists('calendar_id', $this->options)) {
            # get all calendars related through a join on user_has_permission
            $sql = '
                SELECT DISTINCT user.uid FROM calendar
                INNER JOIN user_has_permission ON calendar.id = user_has_permission.calendar_id
                INNER JOIN user ON user_has_permission.user_uid = user.uid
                WHERE calendar.id = "' . (int)($this->options['calendar_id']) . '";';
            return $sql;
        } else if (array_key_exists('not_calendar_id', $this->options)) {
            $sql = '
                SELECT DISTINCT uid FROM user 
                WHERE uid NOT IN (SELECT DISTINCT user.uid FROM calendar
                INNER JOIN user_has_permission ON calendar.id = user_has_permission.calendar_id
                INNER JOIN user ON user_has_permission.user_uid = user.uid
                WHERE calendar.id = "' . (int)($this->options['not_calendar_id']) . '");';
            return $sql;
        } else {
            $sql = 'SELECT DISTINCT uid FROM user';
            return $sql;
        }

        return parent::getSQL();
    }
}