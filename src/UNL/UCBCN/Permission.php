<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;
/**
 * Table Definition for permission
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
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Permission extends Record
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $name;                            // string(100)
    public $description;                     // string(255)
    public $standard;
    
    const EVENT_CREATE = 'Event Create';
    const EVENT_EDIT = 'Event Edit';
    const EVENT_DELETE = 'Event Delete';
    const EVENT_MOVE_TO_UPCOMING = 'Event Post';
    const EVENT_MOVE_TO_PENDING = 'Event Send Event to Pending Queue';
    const EVENT_RECOMMEND = 'Event Recommend';

    const CALENDAR_EDIT = 'Calendar Edit';
    const CALENDAR_DELETE = 'Calendar Delete';
    const CALENDAR_EDIT_PERMISSIONS = 'Calendar Change User Permissions';
    const CALENDAR_EDIT_SUBSCRIPTIONS = 'Calendar Edit Subscriptions';

    const EVENT_CREATE_ID = 25;
    const EVENT_EDIT_ID = 5;
    const EVENT_DELETE_ID = 2;
    const EVENT_MOVE_TO_UPCOMING_ID = 3;
    const EVENT_MOVE_TO_PENDING_ID = 4;
    const EVENT_RECOMMEND_ID = 6;

    const CALENDAR_EDIT_ID = 19;
    const CALENDAR_DELETE_ID = 16;
    const CALENDAR_EDIT_PERMISSIONS_ID = 18;
    const CALENDAR_EDIT_SUBSCRIPTIONS_ID = 22;

    public static function getTable()
    {
        return 'permission';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
}
