<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\RecordList;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\UnexpectedValueException;

/**
 * Object related to a list of event types for a specific event.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

/**
 * This class holds all the events for the list.
 *
 * @package   UNL_UCBCN
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class EventTypes extends RecordList
{
    function __construct($options = array())
    {
        parent::__construct($options);
    }
    
    public function getDefaultOptions()
    {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\EventType',
        );
    }
    
    public function getSQL()
    {
        if (array_key_exists('event_id', $this->options)) {
            return 'SELECT id FROM event_has_eventtype WHERE event_has_eventtype.event_id = ' .
                (int)$this->options['event_id'];
        }

        $sql_output = 'SELECT id FROM eventtype';
        if (array_key_exists('order_name', $this->options) && $this->options['order_name'] === true) {
            $sql_output .= ' ORDER BY name';
        }
        return $sql_output;
    }
}
