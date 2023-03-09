<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;
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
     * Checks to see if the location is saved to anyone or any calendar.
     * It also checks to see if it is a standard location
     * 
     * @return bool
     */
    public function is_saved_or_standard()
    {
        return (isset($this->user_id) || isset($this->calendar_id) || $this->standard == 1);
    }

    /**
     * Checks to see if the location has all information necessary for Google's microdata.
     * 
     * @return bool
     */
    public function microdata_check()
    {
        if (!isset($this->name) || empty($this->name)) {
            return false;
        }

        if (!isset($this->streetaddress1) || empty($this->streetaddress1)) {
            return false;
        }

        if (!isset($this->city) || empty($this->city)) {
            return false;
        }

        if (!isset($this->state) || empty($this->state)) {
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
