<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;

/**
 * Table Definition for webcast
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

/**
 * ORM for a record within the database.
 *
 * @package   UNL_UCBCN
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Webcast extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $title;                           // string(100)
    public $url;                             // blob(4294967295)  blob
    public $additionalinfo;                  // blob(4294967295)  blob
    public $user_id;                         // string(100)
    public $calendar_id;                     // string(100)

    public static function getTable()
    {
        return 'webcast';
    }


    public function table()
    {
        return array(
            'id'=>129,
            'title'=>2,
            'url'=>66,
            'additionalinfo'=>66,
            'user_id'=>2,
            'calendar_id'=>2,
        );
    }

    public function keys()
    {
        return array(
            'id',
        );
    }

    public function sequenceKey()
    {
        return array('id',true);
    }

    public function getCalendar()
    {
        if (!isset($this->calendar_id) || empty($this->calendar_id)) {
            return null;
        }

        return Calendar::getByID($this->calendar_id);
    }

    public function toJSON()
    {
        $data = array(
            'v-location'                            => $this->id,
            'new-v-location-name'                   => $this->title,
            'new-v-location-url'                    => $this->url,
            'new-v-location-additional-public-info' => $this->additionalinfo,
            'user_id'                               => $this->user_id,
            'calendar_id'                           => $this->calendar_id,
        );
        return $data;
    }

    /**
     * Checks to see if the location is saved to anyone or any calendar.
     *
     * @return bool
     */
    public function isSaved()
    {
        return isset($this->user_id) || isset($this->calendar_id);
    }

    /**
     * Checks to see if the location has all information necessary for Google's microdata.
     *
     * @return bool
     */
    public function microdataCheck()
    {
        return isset($this->url) && !empty($this->url);
    }
}
