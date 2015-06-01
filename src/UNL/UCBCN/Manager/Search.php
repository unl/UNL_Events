<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Event;
use UNL\UCBCN\Events;

class Search
{
    public $options = array();
    public $calendar;
    public $search_term;
    public $events;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        if (array_key_exists('search_term', $this->options)) {
            $this->search_term = $this->options['search_term'];
            $this->events = new Events(array('search_term'=>$this->search_term));
        }
    }
}
