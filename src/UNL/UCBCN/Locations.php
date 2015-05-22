<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\RecordList;
use UNL\UCBCN\ActiveRecord\Record;
/**
 * Object related to a list of calendars.
 * 
 * PHP version 5
 * 
 * @category  Events 
 * @package   UNL_UCBCN
 * @author    Tyler Lemburg <trlemburg@gmail.com>
 * @copyright 2015 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

class Locations extends RecordList
{
    function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function getDefaultOptions() {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Location',
        );
    }

    public function getSQL() {
        if (array_key_exists('user_id', $this->options)) {
            return 'SELECT id FROM location WHERE standard = 1 OR user_id = "' . $this->options['user_id'] . '" ORDER BY user_id DESC, display_order ASC, name ASC;';
        } else {
            return parent::getSQL();
        }
    }
}
