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
        parent::__construct($options);
    }

    function getSQL()
    {
        if (array_key_exists('calendar_id', $this->options)) {
            $sql = 'SELECT id FROM subscription WHERE calendar_id = ' . (int)$this->options['calendar_id'] . ';';
            return $sql;
        } else if (array_key_exists('subbed_calendar_id', $this->options)) {
            $sql = 'SELECT subscription_id FROM subscription_has_calendar
                    WHERE calendar_id = ' . (int)$this->options['subbed_calendar_id'] . ';';
            return $sql;
        }

        return parent::getSQL();
    }
}