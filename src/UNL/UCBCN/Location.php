<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Calendar;

/**
 * Details for locations within the database.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

/**
 * ORM for a Location record within the database.
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Location extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $name;                            // string(100)  multiple_key
    public $streetaddress1;                  // string(255)
    public $streetaddress2;                  // string(255)
    public $room;                            // string(100)
    public $city;                            // string(100)
    public $state;                           // string(2)
    public $zip;                             // string(10)
    public $mapurl;                          // blob(4294967295)  blob
    public $webpageurl;                      // blob(4294967295)  blob
    public $hours;                           // string(255)
    public $directions;                      // blob(4294967295)  blob
    public $additionalpublicinfo;            // string(255)
    public $type;                            // string(100)
    public $phone;                           // string(50)
    public $standard;                        // int(1)
    public $user_id;                         // string(255)
    public $calendar_id;                     // string(255)
    public $display_order;                   // int(1)

    const DISPLAY_ORDER_MAIN      = NULL;
    const DISPLAY_ORDER_EXTENSION = 1;

    public static function getTable()
    {
        return 'location';
    }

    function table()
    {
        return array(
            'id'=>129,
            'name'=>2,
            'streetaddress1'=>2,
            'streetaddress2'=>2,
            'room'=>2,
            'city'=>2,
            'state'=>2,
            'zip'=>2,
            'mapurl'=>66,
            'webpageurl'=>66,
            'hours'=>2,
            'directions'=>66,
            'additionalpublicinfo'=>2,
            'type'=>2,
            'phone'=>2,
            'standard'=>17,
        );
    }

    function keys()
    {
        return array(
            'id',
        );
    }

    function sequenceKey()
    {
        return array('id',true);
    }

    // Override Record::synchronizeWithArray to format select location values
    public function synchronizeWithArray($data)
    {
        foreach (get_object_vars($this) as $key=>$default_value) {
            if (isset($data[$key])) {
                switch($key) {
                    case 'phone':
                        $this->$key = Util::formatPhoneNumber($data[$key]);
                        break;

                    default:
                        $this->$key = $data[$key];
                }
            }
        }
    }

    /**
     * Gets the calendar that is saved
     *
     * @return Calendar|false
     */
    public function getCalendar()
    {
        if (!isset($this->calendar_id) || empty($this->calendar_id)) {
            return false;
        }

        return Calendar::getByID($this->calendar_id);
    }

    /**
     * Creates a nicely formatted json data
     *
     * @return array
     */
    public function toJSON(): array
    {
        return array(
            'location'                        => $this->id,
            'location-name'                   => $this->name,
            'location-address-1'              => $this->streetaddress1,
            'location-address-2'              => $this->streetaddress2,
            'location-city'                   => $this->city,
            'location-state'                  => $this->state,
            'location-zip'                    => $this->zip,
            'location-map-url'                => $this->mapurl,
            'location-webpage'                => $this->webpageurl,
            'location-hours'                  => $this->hours,
            'location-phone'                  => $this->phone,
            'location-room'                   => $this->room,
            'location-directions'             => $this->directions,
            'location-additional-public-info' => $this->additionalpublicinfo,
            'user_id'                         => $this->user_id,
            'calendar_id'                     => $this->calendar_id,
        );
    }

    /**
     * Checks to see if the location is saved to anyone or any calendar.
     * It also checks to see if it is a standard location
     *
     * @return bool
     */
    public function isSavedOrStandard()
    {
        return isset($this->user_id) || isset($this->calendar_id) || $this->standard == 1;
    }

    /**
     * Checks to see if the location has all information necessary for Google's microdata.
     *
     * @return bool
     */
    public function microdataCheck()
    {
        if (!isset($this->streetaddress1) || empty($this->streetaddress1)) {
            return false;
        }

        if (!isset($this->city) || empty($this->city)) {
            return false;
        }

        if (!isset($this->state) || empty($this->state)) {
            return false;
        }

        if (!isset($this->zip) || empty($this->zip)) {
            return false;
        }

        return true;
    }
}
