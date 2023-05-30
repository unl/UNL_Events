<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\RecordList;

/**
 * Object related to a list of webcasts.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

/**
 * This class holds all the webcasts for the list.
 *
 * @package   UNL_UCBCN
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class Webcasts extends RecordList
{
    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function getDefaultOptions()
    {
        return array(
            'listClass' =>  __CLASS__,
            'itemClass' => __NAMESPACE__ . '\\Webcast',
        );
    }

    public function getSQL()
    {
        if (array_key_exists('user_id', $this->options)) {
            return '
                SELECT id
                FROM webcast
                WHERE user_id = "' . $this->escapeString($this->options['user_id']) . '"
                ORDER BY title ASC;
            ';
        } else if (array_key_exists('calendar_id', $this->options)) {
            return '
                SELECT id
                FROM webcast
                WHERE calendar_id = "' . $this->escapeString($this->options['calendar_id']) . '"
                ORDER BY title ASC;
            ';
        } else {
            return parent::getSQL();
        }
    }
}
