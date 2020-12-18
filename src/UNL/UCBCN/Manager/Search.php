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
    public $event_type_id;
    public $events;
    public $page;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        if (array_key_exists('page', $_GET) && is_numeric($_GET['page']) && $_GET['page'] >= 1) {
            $this->page = $_GET['page'];
        } else {
            $this->page = 1;
        }

        if (array_key_exists('search_term', $this->options)) {
            $this->search_term = $this->options['search_term'];
            $this->event_type_id = array_key_exists('event_type_id', $this->options) ? $this->options['event_type_id'] : 0;
            $this->events = new Events(array(
                'search_term' => $this->search_term,
                'event_type_id' => $this->event_type_id,
                'limit' => 10,
                'offset' => ($this->page - 1) * 10
            ));
        }
    }
}
