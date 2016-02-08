<?php
namespace UNL\UCBCN\Calendar;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;

# class for many calendar_has_event records
class Events extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Event',
        );
    }

    public function getSQL() {
    	if (array_key_exists('event_id', $this->options)) {
    		return 'SELECT id FROM calendar_has_event
    				WHERE event_id = ' . (int)($this->options['event_id']) . ';';
    	} else if (array_key_exists('calendar_id', $this->options)) {
            if (array_key_exists('status', $this->options)) {
                return 'SELECT id FROM calendar_has_event
                    WHERE calendar_id = ' . (int)($this->options['calendar_id']) . ' AND status = ' . $this->options['status'] . ';';
            }
            return 'SELECT id FROM calendar_has_event
                    WHERE calendar_id = ' . (int)($this->options['calendar_id']) . ';';
        } else {
    		return parent::getSQL();
    	}
    }
}
