<?php
namespace UNL\UCBCN\Calendar;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;

# class for many user_has_permission records
class SubscriptionHasCalendars extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\SubscriptionHasCalendar',
        );
    }

    public function getSQL() {
    	if (array_key_exists('calendar_id', $this->options)) {
    		return 'SELECT id FROM subscription_has_calendar
    				WHERE calendar_id = ' . (int)($this->options['calendar_id']) . ';';
        } else {
    		return parent::getSQL();
    	}
    }
}
