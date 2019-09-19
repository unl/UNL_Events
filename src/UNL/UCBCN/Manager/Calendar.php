<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Manager\Auth;
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

    const HAVE_PURGED_PAST_PENDING_EVENTS = 'HAVE_PURGED_PAST_PENDING_EVENTS';

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        // Auto purge past pending events older than 1 month from calendar on first session visit
        if (!isset($_SESSION[static::HAVE_PURGED_PAST_PENDING_EVENTS . '-' . $this->calendar->id])) {
            $this->calendar->purgePastEventsByStatus(CalendarModel::STATUS_PENDING, CalendarModel::CLEANUP_MONTH_1);
            $_SESSION[static::HAVE_PURGED_PAST_PENDING_EVENTS . '-' . $this->calendar->id] = true;
        }

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!in_array($this->calendar->id, $user->getCalendars()->getIDs())) {
            Controller::redirect(Controller::$url . 'welcome/');
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

        # store tab and page in session
        $_SESSION['current_tab'] = $this->tab;
        $_SESSION['current_page'] = $this->page;
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
        $events = $this->calendar->getEvents(CalendarModel::STATUS_POSTED);
        $archived_events = $this->calendar->getEvents(CalendarModel::STATUS_ARCHIVED);

        # check each event to see if it has passed
        foreach ($events as $event) {
            $archive = $event->isInThePast();

            # update the status with the calendar
            if ($archive) {
                $event->updateStatusWithCalendar($this->calendar, CalendarModel::STATUS_ARCHIVED);
            }
        }

        # check each past event to see if it is now current
        foreach ($archived_events as $event) {
            # we will consider it upcoming if any datetime is after today at midnight
            $datetimes = $event->getDatetimes();
            $move = FALSE;
            foreach ($datetimes as $datetime) {
                $recurring_dates = $datetime->getAllDates();
                foreach($recurring_dates as $recurring_date) {
                    if ($recurring_date->recurringdate >= date('Y-m-d')) {
                        $move = TRUE;
                        break 2;
                    }
                }

                if ($datetime->starttime >= date('Y-m-d 00:00:00') || ($datetime->endtime != NULL && $datetime->endtime >= date('Y-m-d 00:00:00'))) {
                    $move = TRUE;
                    break;
                }
            }

            # update the status with the calendar
            if ($move) {
                $event->updateStatusWithCalendar($this->calendar, CalendarModel::STATUS_POSTED);
            }
        }
    }

}