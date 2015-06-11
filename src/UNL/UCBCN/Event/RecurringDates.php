<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\RecordList;

class RecurringDates extends RecordList
{
    function __construct($options = array())
    {
        if (!isset($options['event_id']) && !isset($options['event_datetime_id'])) {
            throw new RuntimeException('You must pass an event_id or event_datetime_id', 500);
        }
        parent::__construct($options);
    }

    public function getDefaultOptions()
    {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\RecurringDate',
        );
    }

    public function getSQL()
    {
        if (array_key_exists('linked_only', $this->options)) {
            return 'SELECT id FROM recurringdate WHERE ' .
                'event_id = ' . (int)($this->options['event_id']) . ' AND unlinked = 0;';
        } else if (array_key_exists('event_datetime_id', $this->options)) {
            if (array_key_exists('recurrence_id', $this->options)) {
                return 'SELECT id FROM recurringdate WHERE ' .
                    'event_datetime_id = ' . (int)($this->options['event_datetime_id']) . 
                    ' AND recurrence_id = ' . (int)($this->options['recurrence_id']) . ' AND unlinked = 0 AND ongoing = 0 ' .
                    'ORDER BY recurrence_id ASC;';
            } else {
                return 'SELECT id FROM recurringdate WHERE ' .
                    'event_datetime_id = ' . (int)($this->options['event_datetime_id']) . ' AND unlinked = 0 AND ongoing = 0 ' .
                    'ORDER BY recurrence_id ASC;';
            }
        } else {
            return 'SELECT id FROM recurringdate WHERE ' .
                'event_id = ' . (int)($this->options['event_id']) . ';';
        }
    }
}