<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Location as Location;

class UserLocation extends PostHandler
{
    public $options = array();
    public $post;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    // Gets the locations by its id
    public function getLocation($location_id)
    {
        return Location::getById($location_id);
    }

    // Gets the locations saved to the user
    public function getUserLocations()
    {
        return LocationUtility::getUserLocations();
    }

    // Gets all the calendars a user has access to
    public function getUserCalendars()
    {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }

    // Validates a user can edit and create locations on the calendar
    public function userHasAccessToCalendar(string $calendar_id): bool
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
                    $this->createLocation($post);
                    break;
                case "put":
                    $this->updateLocation($post);
                    break;
                case "delete":
                    $this->detachLocation($post);
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
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Created', 'Your Location has been created.');
                break;
            case "put":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Updated', 'Your Location has been updated.');
                break;
            case "delete":
                $this->flashNotice(
                    parent::NOTICE_LEVEL_SUCCESS,
                    'Location Detached',
                    'Your Location has been detached from you.'
                );
                break;
        }

        // Redirect
        return Controller::getUserLocationURL();
    }

    // Create a new location
    private function createLocation(array $post_data)
    {
        // Makes sure it is saved to the user
        $user = Auth::getCurrentUser();
        $post_data['location_save'] = 'on';

        // Validate the calendar if it is set
        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);

            if ($calendar === false) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            // Check if the user has access to the calendar
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        // Validates the location data
        $this->validateLocation($post_data);

        // Makes the new location
        LocationUtility::addLocation($post_data, $user, $calendar);
    }

    // Updates an existing location
    private function updateLocation(array $post_data)
    {
        // Makes sure it is saved to the user
        $user = Auth::getCurrentUser();
        $post_data['location_save'] = 'on';

        // Validate the calendar if it is set
        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);

            if ($calendar === false) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            // Check if the user has access to the calendar
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        // Makes sure we have a location set and it is valid
        if (!empty($post_data['location']) && $post_data['location'] === "New") {
            throw new ValidationException('Missing Location To Update.');
        }
        $location = Location::getByID($post_data['location']);
        if ($location === null) {
            throw new ValidationException('Invalid Location ID');
        }

        // Double check we have access to modify that location
        if (
            !(isset($location->user_id) && $location->user_id === $user->uid) &&
            !(isset($location->calendar_id) && $this->userHasAccessToCalendar($location->calendar_id))
        ) {
            throw new ValidationException('You do not have access to modify that location.');
        } elseif (isset($location->calendar_id) && !$this->userHasAccessToCalendar($location->calendar_id)) {
            $post_data['location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($location->calendar_id);
            $post_data['calendar_id'] = $location->calendar_id;
        }

        // Validates the location data
        $this->validateLocation($post_data);

        // Tries to update and if not we will throw an error
        try {
            LocationUtility::updateLocation($post_data, $user, $calendar);
        } catch(ValidationException $e) {
            throw new ValidationException('Error Updating Location');
        }
    }

    // Detaches a location from the calendar, does not delete it
    private function detachLocation(array $post_data)
    {
        // Checks to see if the location is set and valid
        if (!empty($post_data['location']) && $post_data['location'] === "New") {
            throw new ValidationException('Missing Location To Detach');
        }
        $location = Location::getByID($post_data['location']);
        if ($location === null) {
            throw new ValidationException('Invalid Location ID');
        }

        // Removed the user from it
        $location->user_id = null;
        $location->update();
    }

    // Uses the location utility to validate the location data
    private function validateLocation(array $post_data)
    {
        $validate_data = LocationUtility::validateLocation($post_data);
        if (!$validate_data['valid']) {
            throw new ValidationException($validate_data['message']);
        }
    }
}
