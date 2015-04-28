<?php
namespace UNL\UCBCN\Calendar;

use UNL\UCBCN\ActiveRecord\Record;

/**
 * Table Definition for subscription
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

    function table()
    {
        return array(
            'id'=>129,
            'subscription_id'=>129,
            'calendar_id'=>129
        );
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
    function sequenceKey()
    {
        return array('id',true);
    }
    
    function links()
    {
        return array('calendar_id'    => 'calendar:id',
                     'subscription_id'     => 'subscription:id');
    }
}
