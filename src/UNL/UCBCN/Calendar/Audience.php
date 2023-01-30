<?php
namespace UNL\UCBCN\Calendar;

use UNL\UCBCN\ActiveRecord\Record;

/**
 * Table Definition for audience
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Tommy Neumann <tneumann9@unl.edu>
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

/**
 * ORM for a record within the database.
 *
 * @package   UNL_UCBCN
 * @author    Tommy Neumann <tneumann9@unl.edu>
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Audience extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $name;                            // string(100)
    public $standard;                        // int(1)

    public static function getTable()
    {
        return 'audience';
    }

    public function table()
    {
        return array(
            'id'=>129,
            'name'=>2,
            'standard'=>17,
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

    public function links()
    {
        return array('calendar_id' => 'calendar:id');
    }
}
