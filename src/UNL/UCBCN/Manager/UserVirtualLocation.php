<?php
namespace UNL\UCBCN\Manager;

use Exception;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Webcast as Webcast;

class UserVirtualLocation extends PostHandler
{
    public $options = array();
    public $post;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    // Get webcast from its ID
    public function getWebcast($webcast_id)
    {
        return Webcast::getById($webcast_id);
    }

    // Gets the webcasts that are saved to the user
    public function getUserWebcasts()
    {
        return WebcastUtility::getUserWebcasts();
    }

    // Gets all the calendars saved to the user
    public function getUserCalendars()
    {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }

    // Validates a user can edit and create locations on the calendar
    public function userHasAccessToCalendar(string $calendar_id)
    {
        if (!isset($calendar_id) || empty($calendar_id)) {
            return true;
        }

        $user = Auth::getCurrentUser();

        $edit_permission = Permission::getByName('Event Edit');
        $create_permission = Permission::getByName('Event Create');

        return $user->hasPermission($edit_permission->id, $calendar_id)
            && $user->hasPermission($create_permission->id, $calendar_id);
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
                    'Your Virtual Location has been created.'
                );
                break;
            case "put":
                $this->flashNotice(
                    parent::NOTICE_LEVEL_SUCCESS,
                    'Virtual Location Updated',
                    'Your Virtual Location has been updated.'
                );
                break;
            case "delete":
                $this->flashNotice(
                    parent::NOTICE_LEVEL_SUCCESS,
                    'Virtual Location Detached',
                    'Your Virtual Location has been detached from you.'
                );
                break;
        }

        // Redirect
        return Controller::getUserVirtualLocationURL();
    }

    // Create a new virtual location
    private function createWebcast(array $post_data)
    {
        // Makes sure it is saved to the user
        $user = Auth::getCurrentUser();
        $post_data['v_location_save'] = 'on';

        // Validate the calendar if it is set
        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['v_location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);

            if ($calendar === false) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            // Check if the user has access to the calendar
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        // Validates the virtual location data
        $this->validateWebcast($post_data);

        // Makes the new virtual location
        WebcastUtility::addWebcast($post_data, $user, $calendar);
    }

    // Updates an existing virtual location
    private function updateWebcast(array $post_data)
    {
        // Makes sure it is saved to the user
        $user = Auth::getCurrentUser();
        $post_data['v_location_save'] = 'on';

        // Validate the calendar if it is set
        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['v_location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);

            if ($calendar === false) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            // Check if the user has access to the calendar
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        // Makes sure we have a virtual location set and it is valid
        if (!empty($post_data['v_location']) && $post_data['v_location'] === "New") {
            throw new ValidationException('Missing Virtual Location To Update.');
        }
        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Virtual Location');
        }

        // Double check we have access to modify that virtual location
        if (
            !(isset($webcast->user_id) && $webcast->user_id === $user->uid) &&
            !(isset($webcast->calendar_id) && $this->userHasAccessToCalendar($webcast->calendar_id))
        ) {
            throw new ValidationException('You do not have access to modify that virtual location.');
        } elseif (isset($webcast->calendar_id) && !$this->userHasAccessToCalendar($webcast->calendar_id)) {
            $post_data['v_location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($webcast->calendar_id);
            $post_data['calendar_id'] = $webcast->calendar_id;
        }

        // Validates the virtual location data
        $this->validateWebcast($post_data);

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
            throw new ValidationException('Missing Location To Detach');
        }
        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Virtual Location ID');
        }

        // Removed the user from it
        $webcast->user_id = null;
        $webcast->update();
    }

    // Uses the webcast utility to validate the virtual location data
    private function validateWebcast(array $post_data)
    {
        $validate_data = WebcastUtility::validateWebcast($post_data);
        if (!$validate_data['valid']) {
            throw new ValidationException($validate_data['message']);
        }
    }
}
