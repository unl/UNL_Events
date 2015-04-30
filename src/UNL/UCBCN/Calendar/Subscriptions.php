<?php
namespace UNL\UCBCN\Calendar;
use UNL\UCBCN\ActiveRecord\RecordList;

class Subscriptions extends RecordList
{
    protected $calendar_id;

    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Subscription',
        );
    }

    function __construct($options = array())
    {
        if (!isset($options['calendar_id'])) {
            throw new RuntimeException('You must pass a calendar ID', 500);
        }
        parent::__construct($options);
    }

    function getSQL()
    {
        $sql = 'SELECT id FROM subscription WHERE calendar_id = ' . (int)$this->options['calendar_id'] . ';';
        return $sql;
    }
}