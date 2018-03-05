<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
use UNL\UCBCN\Event;
use UNL\UCBCN\Permission;

class PromoteEvent extends PostHandler
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
        if (!isset($post['event_id'])) {
            throw new \Exception("The event_id must be set in the post data.", 400);
        }

        if ($post['event_id'] != $this->event->id) {
            throw new \Exception("The event_id in the post data must match the event_id in the URL.", 400);
        }

        $event = Event::getByID($this->event->id);
        switch ($post['status']) {
            case 'promote':
                $event->promoted = date('Y-m-d H:i:s');
                $event->save();
                break;
            case 'hide-promo':
                $event->promoted = 'hide';
                $event->save();
                break;
        }
        
        //redirect
        return $this->calendar->getManageURL(TRUE);
    }
}