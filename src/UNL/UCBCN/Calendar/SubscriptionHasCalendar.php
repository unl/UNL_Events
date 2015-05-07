<?php
namespace UNL\UCBCN\Calendar;

use UNL\UCBCN\ActiveRecord\Record;

/**
 * Table Definition for subscription_has_calendar
 */
class SubscriptionHasCalendar extends Record
{
    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $subscription_id;                // int(10)  not_null multiple_key unsigned
    public $calendar_id;                     // int(10)  not_null multiple_key unsigned

    public static function getTable()
    {
        return 'subscription_has_calendar';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
}
