<?php
namespace UNL\UCBCN\User;

use UNL\UCBCN\ActiveRecord\Record;
/**
 * Table Definition for user_has_permission
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
    public $permission_id;                   // int(10)  not_null unsigned
    public $user_uid;                        // string(100)  not_null
    public $calendar_id;                     // int(10)  not_null unsigned

    public static function getTable()
    {
        return 'user_has_permission';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
}
