<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\Event as CalendarHasEvent;
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

    const HAVE_PROCESSED_CALENDAR_EVENTS = 'HAVE_PROCESSED_CALENDAR_EVENTS';

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!in_array($this->calendar->id, $user->getCalendars()->getIDs())) {
            Controller::redirect(Controller::$url . 'welcome/');
        }

        # Process events if session check is not set (first session visit)
        if (!isset($_SESSION[static::HAVE_PROCESSED_CALENDAR_EVENTS . '-' . $this->calendar->id])) {

            # Auto purge past pending events older than 1 month from calendar
            $this->calendar->purgePastEventsByStatus(CalendarModel::STATUS_PENDING, CalendarModel::CLEANUP_MONTH_1);

            # Correctly set past event status based on time
            $this->calendar->archivePastEvents();

            # Set session variable so we don't run the above again for this calendar this session (unless it's removed)
            $_SESSION[static::HAVE_PROCESSED_CALENDAR_EVENTS . '-' . $this->calendar->id] = true;
        }

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

}