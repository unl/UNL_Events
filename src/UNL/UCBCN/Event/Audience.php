<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Event;

/**
 * Table Definition for audience
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
class Audience extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $event_id;                        // int(10)  not_null multiple_key unsigned
    public $audience_id;                    // int(10)  not_null multiple_key unsigned

    public static function getTable()
    {
        return 'event_targets_audience';
    }

    public function keys()
    {
        return array(
            'id',
        );
    }
    
    /**
     * Get the audience record (details) for this link
     *
     * @return false|\UNL\UCBCN\Calendar\Audience - the audience record or false
     */
    public function getAudience()
    {
        return \UNL\UCBCN\Calendar\Audience::getById($this->audience_id);
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
