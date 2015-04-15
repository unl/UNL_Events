<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\RecordList;

class Occurrences extends RecordList
{
    function __construct($options = array())
    {
        if (!isset($options['event_id'])) {
            throw new RuntimeException('You must pass an event_id', 500);
        }
        parent::__construct($options);
    }

    public function getDefaultOptions()
    {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Occurrence',
        );
    }

    public function getSQL()
    {
        if (array_key_exists('recurring_only', $this->options)) {
            return 'SELECT id FROM eventdatetime WHERE ' .
                'event_id = ' . (int)($this->options['event_id']) . ' AND recurringtype != "none";';
        } else {
            return 'SELECT id FROM eventdatetime WHERE ' .
                'event_id = ' . (int)($this->options['event_id']) . ';';
        }
    }
}