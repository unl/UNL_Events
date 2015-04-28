<?php
namespace UNL\UCBCN\Calendar;
use UNL\UCBCN\ActiveRecord\RecordList;

class Subscribers extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Subscription',
        );
    }
    
    function __construct($options = array())
    {
        if (!isset($options['calendar_id'])) {
            throw new RuntimeException('You must pass a calendar id', 500);
        }
        parent::__construct($options);
    }

    public function getSQL()
    {
        return 'SELECT * FROM subscriptions WHERE searchcriteria LIKE "%calendar_has_event.calendar_id='.(int)$calendar_id.' %"';
    }
}