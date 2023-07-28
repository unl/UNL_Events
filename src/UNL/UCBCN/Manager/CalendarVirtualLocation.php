<?php
namespace UNL\UCBCN\Manager;

use Exception;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Webcast as Webcast;

class CalendarVirtualLocation extends PostHandler
{
    public $options = array();
    public $post;
    public $calendar;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);

        if (!$this->userHasAccessToCalendar()) {
            throw new \Exception("You do not have permission to add/edit virtual locations.", 403);
        }
    }

    // Gets the webcasts that are saved to the calendar
    public function getCalendarWebcasts()
    {
        return WebcastUtility::getCalendarWebcasts($this->calendar->id);
    }

    public function getCurrentUser()
    {
        $user = Auth::getCurrentUser();

        return $user->uid;
    }

    // Gets all the calendars saved to the user
    public function getUserCalendars()
    {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }

    // Validates a user can edit and create locations on the calendar
    public function userHasAccessToCalendar(): bool
    {
        $user = Auth::getCurrentUser();

        $edit_permission = Permission::getByName('Event Edit');
        $create_permission = Permission::getByName('Event Create');

        return $user->hasPermission($edit_permission->id, $this->calendar->id)
            && $user->hasPermission($create_permission->id, $this->calendar->id);
    }

    // Handles all the forms submissions
    public function handlePost(array $get, array $post, array $files)
    {
        // Determine what to do based on the method inputted
        $method = $post['method'] ?? "";
        try {
            switch ($method) {
                case "post":
                    $this->createWebcast($post);
                    break;
                case "put":
                    $this->updateWebcast($post);
                    break;
                case "delete":
                    $this->detachWebcast($post);
                    break;
                default:
                    throw new ValidationException('Invalid Method');
            }

        } catch (ValidationException $e) {
            $this->post = $post;
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t create your event', $e->getMessage());
            throw $e;
        }

        // If everything goes well we will output a success notice based on the method
        switch ($method) {
            case "post":
                $this->flashNotice(
                    parent::NOTICE_LEVEL_SUCCESS,
                    'Virtual Location Created',
                    'Your calendar\'s virtual location has been created.'
                );
                break;
            case "put":
                $this->flashNotice(
                    parent::NOTICE_LEVEL_SUCCESS,
                    'Virtual Location Updated',
                    'Your calendar\'s virtual location has been updated.'
                );
                break;
            case "delete":
                $this->flashNotice(
                    parent::NOTICE_LEVEL_SUCCESS,
                    'Virtual Location Detached',
                    'The virtual location has been detached from your calendar.'
                );
                break;
        }

        // Redirect
        return $this->calendar->getVirtualLocationURL();
    }

    // Create a new virtual location
    private function createWebcast(array $post_data)
    {
        $user = Auth::getCurrentUser();

        // Makes sure it is saved to the calendar you made it on
        $post_data['v_location_save_calendar'] = 'on';
        $calendar = $this->calendar;

        // Validates the virtual location data
        $this->validateLocation($post_data);

        // Makes the new virtual location
        WebcastUtility::addWebcast($post_data, $user, $calendar);
    }

    // Updates an existing virtual location
    private function updateWebcast(array $post_data)
    {
        $user = Auth::getCurrentUser();

        // Makes sure it is saved to the calendar you made it on
        $post_data['v_location_save_calendar'] = 'on';
        $calendar = $this->calendar;

        // Makes sure we have a virtual location set and it is valid
        if (!empty($post_data['v_location']) && $post_data['v_location'] === "New") {
            throw new ValidationException('Missing Virtual Location To Update');
        }
        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Virtual Location');
        }

        // Double check we have access to modify that virtual location
        if (
            !(isset($webcast->user_id) && $webcast->user_id === $user->uid) &&
            !(isset($webcast->calendar_id) && $this->userHasAccessToCalendar())
        ) {
            throw new ValidationException('You do not have access to modify that virtual location');
        }

        // Validates the virtual location data
        $this->validateLocation($post_data);

        // Tries to update and if not we will throw an error
        try {
            WebcastUtility::updateWebcast($post_data, $user, $calendar);
        } catch(ValidationException $e) {
            throw new ValidationException('Error Updating Virtual Location');
        }
    }

    // Detaches a location from the calendar, does not delete it
    private function detachWebcast(array $post_data)
    {
        // Checks to see if the virtual location is set and valid
        if (!empty($post_data['v_location']) && $post_data['v_location'] === "New") {
            throw new ValidationException('Missing Virtual Location To Detach');
        }
        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Virtual Location ID');
        }

        // Removed the calendar from it
        $webcast->calendar_id = null;
        $webcast->update();
    }

    // Uses the webcast utility to validate the virtual location data
    private function validateLocation(array $post_data)
    {
        $validate_data = WebcastUtility::validateWebcast($post_data);
        if (!$validate_data['valid']) {
            throw new ValidationException($validate_data['message']);
        }
    }
}
