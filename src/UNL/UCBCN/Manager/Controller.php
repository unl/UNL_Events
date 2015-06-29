<?php
/**
 * This is the primary viewing interface for the events.
 * This would be the 'model/controller' if you follow that paradigm.
 *
 * This file contains functions used throughout the frontend views.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Manger
 * @author    Tyler Lemburg <lemburg@unl.edu>
 * @copyright 2015 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\RuntimeException;
use UNL\UCBCN\UnexpectedValueException;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;

class Controller {
    public $options = array(
        'format' => 'html',
        'model' => false
    );

    /**
     * @var false|Calendar
     */
    protected $calendar = false;

    /**
     * Configurable ID for the base/master calendar
     *
     * @var int
     */
    public static $default_calendar_id = 1;

    public static $url = '/manager/';
    
    public function __construct($options = array()) {
        $this->options = $options + $this->options;

        if (array_key_exists('calendar_shortname', $this->options)) {
            $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
            if ($this->calendar === FALSE) {
                throw new \Exception("That calendar could not be found.", 500);
            }
        }

        try {
            $this->run();
        } catch (\Exception $e) {
            $this->output = $e;
        }

    }

    /**
     * Get the URL to the manager
     *
     * @return string
     */
    public function getURL()
    {
        return self::$url;
    }

    public static function getEditAccountURL() 
    {
        return self::$url . 'account/';
    }

    /**
     * Runs/builds the manager object with the display parameters set.
     * This function will populate all of the output and member variables with the
     * data for the current view.
     *
     * @return void
     * @throws Exception if view is unregistered
     */
    public function run()
    {
        if (is_callable($this->options['model'])) {
            $this->output = call_user_func($this->options['model'], $this->options);
        } else {
            $this->output = new $this->options['model']($this->options);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handlePost($this->output);
        }
    }

    public function getNotice()
    {
        if (isset($_SESSION['flash_notice'])) {
            $notice = $_SESSION['flash_notice'];
            unset($_SESSION['flash_notice']);
            return $notice;
        } else {
            return NULL;
        }
    }
    
    protected function handlePost($object)
    {
        if (!$object instanceof PostHandler) {
            throw new \Exception("The object is not an instance of the PostHandler", 500);
        }
        
        $result = $object->handlePost($_GET, $_POST, $_FILES);
        
        if (is_string($result)) {
            self::redirect($result);
        }

        return $result;
    }

    /**
     * Add a file extension to the existing URL
     *
     * @param string $url       The URL
     * @param string $extension The file extension to add, e.g. csv
     */
    public static function addURLExtension($url, $extension)
    {
        $extension = trim($extension, '.');

        return preg_replace('/^([^?]+)(\.[\w]+)?(\?.*)?$/', '$1.'.$extension.'$3', $url);
    }

    /**
     * Add unique querystring parameters to a URL
     *
     * @param string $url               The URL
     * @param array  $additional_params Additional querystring parameters to add
     *
     * @return string
     */
    public static function addURLParams($url, $additional_params = array())
    {
        $params = self::getURLParams($url);

        $params = array_merge($params, $additional_params);

        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }

        $url .= '?';

        foreach ($params as $option=>$value) {
            if ($option == 'driver') {
                continue;
            }
            if ($option == 'format'
                && $value == 'html') {
                continue;
            }
            if (isset($value)) {
                if (is_array($value)) {
                    foreach ($value as $arr_value) {
                        $url .= "&{$option}[]=$arr_value";
                    }
                } else {
                    $url .= "&$option=$value";
                }
            }
        }
        $url = str_replace('?&', '?', $url);

        return trim($url, '?;=');
    }

    /**
     * Get the querystring parameters for a URL
     *
     * @param string $url 
     *
     * @return array key=>value pairs
     */
    public static function getURLParams($url)
    {
        $params = array();
        if (strpos($url, '?') !== false) {
            list($url, $existing_params) = explode('?', $url);
            $existing_params = explode('&', html_entity_decode($existing_params, ENT_QUOTES, 'UTF-8'));
            foreach ($existing_params as $val) {
                $split = explode('=', $val);
                $params[$split[0]] = '';
                if (isset($split[1])) {
                    $params[$split[0]] = $split[1];
                }
            }
        }

        return $params;
    }

    public static function redirect($url, $exit = true)
    {
        header('Location: '.$url);
        if (!defined('CLI')
            && false !== $exit) {
            exit($exit);
        }
    }

    public function getCalendars() {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }

    /**
     * @return Calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }
}
