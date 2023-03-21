<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\RecordList;

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
class Audiences extends RecordList
{
    public function __construct($options = array())
    {
        parent::__construct($options);
    }
    
    public function getDefaultOptions()
    {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Audience',
        );
    }
    
    public function getSQL()
    {
        if (array_key_exists('event_id', $this->options)) {
            return 'SELECT id FROM event_targets_audience WHERE event_targets_audience.event_id = ' .
                (int)$this->options['event_id'];
        }

        $sql_output = 'SELECT id FROM audience';
        // if (array_key_exists('order_name', $this->options) && $this->options['order_name'] !== NULL) {
        //     $sql_output .= 'ORDER BY name';
        // }
        return $sql_output;
    }
}
