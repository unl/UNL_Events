<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN as BaseUCBCN;

class CreateCalendar extends PostHandler
{
    public $options = array();
    public $calendar;
    public $user;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        if (array_key_exists('user', $this->options)) {
            $this->user = $this->options['user'];
        }

        # check if we are looking to edit a calendar
        if (array_key_exists('calendar_shortname', $this->options)) {
            $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

            if ($this->calendar === FALSE) {
                throw new \Exception("That calendar could not be found.", 404);
            }

            # check permissions to edit this calendar's details
            $user = $this->options['user'] ?? Auth::getCurrentUser();
            if (!$user->hasPermission(Permission::CALENDAR_EDIT_ID, $this->calendar->id)) {
                throw new \Exception("You do not have permission to edit the details of this calendar.", 403);
            }
        } else {
            # we are creating a new calendar
            $this->calendar = new Calendar;
            $this->calendar->eventreleasepreference = Calendar::EVENT_RELEASE_PREFERENCE_IMMEDIATE;
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        if ($this->calendar->id != NULL) {
            # updating a current calendar
            try {
                $this->updateCalendar($post);
            } catch (ValidationException $e) {
                $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t update your calendar', $e->getMessage());
                throw $e;
            }

            $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Calendar Updated', 'Your calendar "' . $this->calendar->name . '" has been updated.');
        } else {
            # we are creating a new calendar
            try {
                $this->createCalendar($post);
            } catch (ValidationException $e) {
                $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t create your calendar', $e->getMessage());
                throw $e;
            }

            $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Calendar Created', 'Your calendar "' . $this->calendar->name . '" has been created.');
        }

        # redirect
        return $this->calendar->getManageURL();
    }

    # this function takes the post and turns it into the calendar data
    # used in create/update functions just to put the data in.
    private function setCalendarData($post_data)
    {
        $this->calendar->name = $post_data['name'];
        $this->calendar->shortname = $post_data['shortname'];
        $this->calendar->defaulttimezone = empty($post_data['defaulttimezone']) ? BaseUCBCN::$defaultTimezone : $post_data['defaulttimezone'];
        $this->calendar->website = $post_data['website'] ?? "";
        switch ($post_data['event_release_preference']) {
            case '':
                $this->calendar->eventreleasepreference = Calendar::EVENT_RELEASE_PREFERENCE_DEFAULT;
                break;
            case 'immediate':
                $this->calendar->eventreleasepreference = Calendar::EVENT_RELEASE_PREFERENCE_IMMEDIATE;
                break;
            case 'pending':
                $this->calendar->eventreleasepreference = Calendar::EVENT_RELEASE_PREFERENCE_PENDING;
                break;
            default:
                $this->calendar->eventreleasepreference = Calendar::EVENT_RELEASE_PREFERENCE_DEFAULT;
        }

        $this->calendar->emaillists = $post_data['email_lists'] ?? "";
        $this->calendar->recommendationswithinaccount = array_key_exists('recommend_within_account', $post_data) &&
            $post_data['recommend_within_account'] == 'on' ? 1 : 0;
    }

    # this function looks at the posted calendar data to ensure its integrity
    private function validateCalendarData($post_data)
    {
        $invalid_shortnames = array(
            'manager',
            'api',
            'www',
            'templates',
            'me',
            'welcome',
            'account',
            'calendar',
            'events',
            'event',
            'audience',
            'eventtype',
            'images',
            'phpMyAdmin'
        );

        # name and shortname are required
        if (empty($post_data['name']) || empty($post_data['shortname'])) {
            throw new ValidationException('Calendar name and shortname are required.');
        }

        # check that the shortname will match the regex it needs to
        if (!preg_match('/^[a-zA-Z-_0-9]+$/', $post_data['shortname'])) {
            throw new ValidationException('Calendar shortnames must contain only letters, numbers, dashes, and underscores.');
        }

        # timezone must be valid
        if (empty($post_data['defaulttimezone']) || !(in_array($post_data['defaulttimezone'], BaseUCBCN::getTimezoneOptions()))) {
            throw new ValidationException('The timezone is invalid.');
        }

        # check if this shortname is already being used
        if (($server_cal = Calendar::getByShortname($post_data['shortname'])) != NULL && $server_cal->id != $this->calendar->id) {
            throw new ValidationException('That shortname is already in use.');
        }

        # check if the shortname is in the list of invalids
        if (in_array($post_data['shortname'], $invalid_shortnames)) {
            throw new ValidationException('Sorry, that shortname is invalid. Please try another one.');
        }

        if (!empty($post_data['event_release_preference']) &&
            !in_array(
                $post_data['event_release_preference'],
                array( 'immediate', 'pending', '' )
            )
        ) {
            throw new ValidationException('Invalid event release preference.');
        }

        if (!empty($post_data['website']) &&
            !filter_var($post_data['website'], FILTER_VALIDATE_URL)
        ) {
            throw new ValidationException('That website is invalid.');
        }
    }

    public function createCalendar($post_data)
    {
        $user = $this->options['user'] ?? Auth::getCurrentUser();
        $account = $user->getAccount();

        $this->validateCalendarData($post_data);
        $this->setCalendarData($post_data);
        $this->calendar->account_id = $account->id;

        $this->calendar->datecreated = date('Y-m-d H:i:s');
        $this->calendar->uidcreated = $user->uid;
        $this->calendar->datelastupdated = date('Y-m-d H:i:s');
        $this->calendar->uidlastupdated = $user->uid;

        $this->calendar->insert();
        $this->calendar->addUser($user);
    }

    public function updateCalendar($post_data)
    {
        $user = $this->options['user'] ?? Auth::getCurrentUser();

        $this->validateCalendarData($post_data);
        $this->setCalendarData($post_data);
        $this->calendar->datelastupdated = date('Y-m-d H:i:s');
        $this->calendar->uidlastupdated = $user->uid;

        $this->calendar->update();
    }

    public function calendarDeletePermission() {
        # check permissions to edit this calendar's details

        $user = $this->options['user'] ?? Auth::getCurrentUser();
        return ($user->hasPermission(Permission::CALENDAR_DELETE_ID, $this->calendar->id));
    }
}