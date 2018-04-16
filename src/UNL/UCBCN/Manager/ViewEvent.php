<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Event;

class ViewEvent
{
    public $options = array();
    public $calendar;
    public $event;
    public $on_main_calendar;
    public $page;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_EDIT_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit events on this calendar.", 403);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        if (array_key_exists('page', $_GET) && is_numeric($_GET['page']) && $_GET['page'] >= 1) {
            $this->page = $_GET['page'];
        } else {
            $this->page = 1;
        }

        $main_calendar = CalendarModel::getByID(Controller::$default_calendar_id);
        $this->on_main_calendar = $this->event->getStatusWithCalendar($main_calendar);
    }

    public function getImageURL()
    {
        if (isset($this->event->imageurl)) {
            return $this->event->imageurl;
        } elseif (isset($this->event->imagedata)) {
            return \UNL\UCBCN\Frontend\Controller::$url . 'images/' . $this->event->id;
        }

        return false;
    }
}
