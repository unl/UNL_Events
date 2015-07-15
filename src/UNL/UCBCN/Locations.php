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
            return '
              SELECT id
              FROM location 
              WHERE user_id = "' . $this->escapeString($this->options['user_id']) . '" 
              ORDER BY display_order ASC, name ASC;
            ';
        } else if (array_key_exists('standard', $this->options)) {
            if (!array_key_exists('display_order', $this->options)) {
                throw new \Exception('You must also provide a display_order filter', 500);
            }
            
            if (NULL == $this->options['display_order']) {
                return '
                  SELECT id
                  FROM location 
                  WHERE standard = 1 AND display_order IS NULL
                  ORDER BY user_id DESC, name ASC;
                ';
            } else {
                return '
                  SELECT id
                  FROM location 
                  WHERE standard = 1 AND display_order = ' . (int)$this->options['display_order'] . '
                  ORDER BY user_id DESC, name ASC;
                ';
            }
            
        } else {
            return parent::getSQL();
        }
    }
}
