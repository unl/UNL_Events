<?php
namespace UNL\UCBCN\User;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;

# class for many user_has_permission records
class Permissions extends RecordList
{
    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Permission',
        );
    }

    public function getSQL() {
    	if (array_key_exists('uid', $this->options)) {
    		return 'SELECT id FROM user_has_permission
    				WHERE user_uid = "' . (int)($this->options['uid']) . '";';
    	} else if (array_key_exists('calendar_id', $this->options)) {
            return 'SELECT id FROM user_has_permission
                    WHERE calendar_id = ' . (int)($this->options['calendar_id']) . ';';
        } else {
    		return parent::getSQL();
    	}
    }
}
