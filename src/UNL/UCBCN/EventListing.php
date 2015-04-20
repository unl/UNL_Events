<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\RecordList;

use UNL\UCBCN\ActiveRecord\Record;
/**
 * Object related to a list of events.
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
 * This class holds all the events for the list.
 * 
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class EventListing extends RecordList
{
    public function getDefaultOptions()
    {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Event\\Occurrence',
        );
    }

    public function getSQL() 
    {
        if (array_key_exists('event_id', $this->options)) {
            return 'SELECT id FROM eventdatetime WHERE event_id = ' . (int)($this->options['event_id']) . ';';
        } else {
            return parent::getSQL();
        }
    }
}
