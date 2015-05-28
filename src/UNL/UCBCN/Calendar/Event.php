<?php
namespace UNL\UCBCN\Calendar;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Manager\Auth;
/**
 * Table definition, and processing functions for calendar_has_event
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
 * ORM for a record within the database.
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Event extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $calendar_id;                     // int(10)  not_null multiple_key unsigned
    public $event_id;                        // int(10)  not_null multiple_key unsigned
    public $status;                          // string(100)  multiple_key
    public $source;                          // string(100)
    public $datecreated;                     // datetime(19)  binary
    public $uidcreated;                      // string(100)
    public $datelastupdated;                 // datetime(19)  binary
    public $uidlastupdated;                  // string(100)
    
    const SOURCE_CREATE_EVENT_FORM      = 'create event form';
    const SOURCE_CHECKED_CONSIDER_EVENT = 'checked consider event';

    public static function getTable()
    {
        return 'calendar_has_event';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
    public static function getById($calendar_id, $event_id)
    {
        return self::getByAnyField(__CLASS__, 'calendar_id', $calendar_id, 'event_id = '.(int)$event_id);
    }
    
    /**
     * Performs an insert of a calendar_has_event record
     *
     * @return int ID of the inserted record.
     */
    public function insert()
    {
        $this->datecreated = date('Y-m-d H:i:s');
        $this->datelastupdated = date('Y-m-d H:i:s');
        $this->uidcreated = Auth::getCurrentUser()->uid;
        $this->uidlastupdated = Auth::getCurrentUser()->uid;
        $result = parent::insert();

        return $result;
    }
    
    /**
     * Performs an update on this calendar_has_event record.
     *
     * @return bool True on sucess
     */
    public function update()
    {
        $this->datelastupdated = date('Y-m-d H:i:s');
        $this->uidlastupdated = Auth::getCurrentUser()->uid;

        $result = parent::update();

        return $result;
    }

    /**
     * Removes this record - effectively removing this event from the calendar.
     *
     * @return bool true on success.
     */
    public function delete()
    {
        $r = parent::delete();

        return $r;
    }
}
