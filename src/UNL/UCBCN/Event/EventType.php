<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Event;

/**
 * Table Definition for event_has_eventtype
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
class EventType extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $event_id;                        // int(10)  not_null multiple_key unsigned
    public $eventtype_id;                    // int(10)  not_null multiple_key unsigned

    public static function getTable()
    {
        return 'event_has_eventtype';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
    /**
     * Get the event type record (details) for this link
     * 
     * @return false|\UNL\UCBCN\Calendar\EventType - the event type record or false
     */
    public function getType()
    {
        return \UNL\UCBCN\Calendar\EventType::getById($this->eventtype_id);
    }

    /**
     * Get the event for this link
     * 
     * @return false|Event - the event record or false
     */
    public function getEvent()
    {
        return Event::getById($this->event_id);
    }
}
