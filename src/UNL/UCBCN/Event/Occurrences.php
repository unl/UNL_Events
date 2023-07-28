<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\RecordList;

class Occurrences extends RecordList
{
    function __construct($options = array())
    {
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
        } elseif (array_key_exists('event_id', $this->options)) {
            return 'SELECT id FROM eventdatetime WHERE ' .
                'event_id = ' . (int)($this->options['event_id']) . ';';
        } elseif (array_key_exists('all_recurring', $this->options)) {
            return 'SELECT id FROM eventdatetime WHERE recurringtype != "none";';
        } elseif (array_key_exists('location_id', $this->options) && array_key_exists('calendar_id', $this->options)) {
            return 'SELECT distinct eventdatetime.id FROM eventdatetime
                inner join event on eventdatetime.event_id = event.id
                inner join calendar_has_event on calendar_has_event.event_id = event.id
                inner join location on eventdatetime.location_id = location.id
                where location.id = ' . (int)($this->options['location_id']) . '
                and calendar_has_event.calendar_id = ' . (int)($this->options['calendar_id']) . ';';
        } elseif (array_key_exists('location_id', $this->options)) {
            return 'SELECT eventdatetime.id FROM eventdatetime
                inner join location on eventdatetime.location_id = location.id
                where location.id = ' . (int)($this->options['location_id']) . ';';
        } elseif (array_key_exists('webcast_id', $this->options) && array_key_exists('calendar_id', $this->options)) {
            return 'SELECT distinct eventdatetime.id FROM eventdatetime
                inner join event on eventdatetime.event_id = event.id
                inner join calendar_has_event on calendar_has_event.event_id = event.id
                inner join webcast on eventdatetime.webcast_id = webcast.id
                where webcast.id = ' . (int)($this->options['webcast_id']) . '
                and calendar_has_event.calendar_id = ' . (int)($this->options['calendar_id']) . ';';
        } elseif (array_key_exists('webcast_id', $this->options)) {
            return 'SELECT eventdatetime.id FROM eventdatetime
                inner join webcast on eventdatetime.webcast_id = webcast.id
                where webcast.id = ' . (int)($this->options['webcast_id']) . ';';
        } else {
            return parent::getSQL();
        }
    }
}