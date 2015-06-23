<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\User;
use UNL\UCBCN\Permission;

class Calendar {
    public $options = array();

    /**
     * @var \UNL\UCBCN\Calendar
     */
    public $calendar;
    public $tab;
    public $page;

    public function __construct($options = array()) {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        # this function will currently run every time the page is loaded. In the future, it would be better
        # to simply decide whether an event should be archived or posted based on its dates, instead
        # of a column that we set in the database
        $this->archiveEvents();

        $allowed_tabs = array('pending', 'upcoming', 'past');
        if (array_key_exists('tab', $_GET) && in_array($_GET['tab'], $allowed_tabs)) {
            $this->tab = $_GET['tab'];
        } else {
            $this->tab = 'pending';
        }

        if (array_key_exists('page', $_GET) && is_numeric($_GET['page']) && $_GET['page'] >= 1) {
            $this->page = $_GET['page'];
        } else {
            $this->page = 1;
        }
    }

    public function getCategorizedEvents()
    {
        $categories = array(
            'pending'  => $this->calendar->getEvents(CalendarModel::STATUS_PENDING),
            'posted'   => $this->calendar->getEvents(CalendarModel::STATUS_POSTED),
            'archived' => $this->calendar->getEvents(CalendarModel::STATUS_ARCHIVED)
        );

        return $categories;
    }

    public function getEvents() 
    {
        $events = NULL;
        switch ($this->tab) {
            case 'pending':
                $events = $this->calendar->getEvents(CalendarModel::STATUS_PENDING, 10, ($this->page-1)*10);
                break;
            case 'upcoming':
                $events = $this->calendar->getEvents(CalendarModel::STATUS_POSTED, 10, ($this->page-1)*10);
                break;
            case 'past':
                $events = $this->calendar->getEvents(CalendarModel::STATUS_ARCHIVED, 10, ($this->page-1)*10);
                break;
            default:
                throw new \Exception("Invalid category of events.", 500);
                break;
        }
            
        return $events;
    }

    private function archiveEvents() {
        # find all posted (upcoming) events on the calendar
        $events = $this->calendar->getEvents('posted');

        # check each event to see if it has passed
        foreach ($events as $event) {
            # we will consider it passed if all datetimes are in the past
            $datetimes = $event->getDatetimes();
            $archive = true;
            foreach ($datetimes as $datetime) {
                if ($datetime->starttime >= date('Y-m-d 00:00:00') || ($datetime->endtime != NULL && $datetime->endtime >= date('Y-m-d 00:00:00'))) {
                    $archive = false;
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