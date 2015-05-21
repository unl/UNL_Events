<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Event;

class DeleteEvent implements PostHandlerInterface
{
    public $options = array();
    public $calendar;
    public $event;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->event->delete();
        
        //redirect
        return $this->calendar->getManageURL();
    }
}