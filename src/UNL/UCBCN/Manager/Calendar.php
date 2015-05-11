<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\User;

class Calendar {
    public $options = array();

    public $calendar;

    public function __construct($options = array()) {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 500);
        }

        # this function will currently run every time the page is loaded. In the future, it would be better
        # to simply decide whether an event should be archived or posted based on its dates, instead
        # of a column that we set in the database
        $this->archiveEvents();
    }

    public function getCategorizedEvents() {
        $events = $this->calendar->getEvents();

        $categories = array(
            'pending' => array(),
            'posted' => array(),
            'archived' => array(),
            'other' => array()
        );

        foreach ($events as $event) {
            $status = $event->getStatusWithCalendar($this->calendar);
            $key = array_key_exists($status, $categories) ? $status : 'other';

            $categories[$key][] = $event;
        }

        return $categories;
    }

    private function archiveEvents() {
        # find all posted (upcoming) events on the calendar
        $events = $this->calendar->getEvents('posted');

        # check each event to see if it has passed
        foreach ($events as $event) {
            # we will consider it passed if one datetime is in the past
            # (this is how the previous system did it)
            $datetimes = $event->getDatetimes();
            $archive = false;
            foreach ($datetimes as $datetime) {
                if ($datetime->starttime < date('Y-m-d 00:00:00') && ($datetime->endtime == NULL || $datetime->endtime < date('Y-m-d 00:00:00'))) {
                    $archive = true;
                    break;
                }
            }

            # update the status with the calendar
            if ($archive) {
                $event->updateStatusWithCalendar($this->calendar, 'archived');
            }
        }
    }

}