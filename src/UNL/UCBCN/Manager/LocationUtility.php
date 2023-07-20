<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Location as Location;
use UNL\UCBCN\Locations;

class LocationUtility
{
    // Function for validating a location's data
    public static function validateLocation(array $post_data): array {
        $outputMessage = "";
        $isValid = true;
        if (empty($post_data['new_location']['name'])) {
            $outputMessage = 'You must give your new location a <a href="#location-name">name</a>.';
            $isValid = false;
        }

        if ($isValid && empty($post_data['new_location']['streetaddress1'])) {
            $outputMessage = 'You must give your new location an <a href="#location-address-1">address</a>.';
            $isValid = false;
        }

        if ($isValid && empty($post_data['new_location']['city'])) {
            $outputMessage = 'You must give your new location a <a href="#location-city">city</a>.';
            $isValid = false;
        }

        if ($isValid && empty($post_data['new_location']['state'])) {
            $outputMessage = 'You must give your new location a <a href="#location-state">state</a>.';
            $isValid = false;
        }

        if ($isValid && empty($post_data['new_location']['zip'])) {
            $outputMessage = 'You must give your new location a <a href="#location-zip">zip</a>.';
            $isValid = false;
        }

        if ($isValid && !empty($post_data['new_location']['webpageurl']) &&
            !filter_var($post_data['new_location']['webpageurl'], FILTER_VALIDATE_URL)
        ) {
            $outputMessage = '<a href="#location-webpage">Location URL</a> is not a valid URL.';
            $isValid = false;
        }

        if ($isValid && !empty($post_data['new_location']['mapurl']) &&
            !filter_var($post_data['new_location']['mapurl'], FILTER_VALIDATE_URL)
        ) {
            $outputMessage = '<a href="#location-mapurl">Location Map URL</a> is not a valid URL.';
            $isValid = false;
        }

        return array("valid" => $isValid, "message" => $outputMessage);
    }

    // Function for creating a new location
    public static function addLocation(array $post_data, $user, $calendar)
    {
        $allowed_fields = array(
            'name',
            'streetaddress1',
            'streetaddress2',
            'room',
            'city',
            'state',
            'zip',
            'mapurl',
            'webpageurl',
            'hours',
            'directions',
            'additionalpublicinfo',
            'type',
            'phone',
        );

        // creates a new Location and fills the values 
        $location = new Location;
        foreach ($allowed_fields as $field) {
            if (!empty($post_data['new_location'][$field])) {
                $location->$field = $post_data['new_location'][$field];
            }
        }

        // If location is saved to user set the user uid
        if (array_key_exists('location_save', $post_data) && $post_data['location_save'] == 'on') {
            $location->user_id = $user->uid;
        }

        // If location is saved to calendar then set the calendar id
        if (array_key_exists('location_save_calendar', $post_data) && $post_data['location_save_calendar'] == 'on') {
            $location->calendar_id = $calendar->id;
        }

        // Do not allow standard to be set
        $location->standard = 0;

        $location->insert();

        return $location;
    }

    // Function for updating an existing location
    public static function updateLocation(array $post_data, $user, $calendar)
    {
        $allowed_fields = array(
            'name',
            'streetaddress1',
            'streetaddress2',
            'room',
            'city',
            'state',
            'zip',
            'mapurl',
            'webpageurl',
            'hours',
            'directions',
            'additionalpublicinfo',
            'type',
            'phone',
        );

        // Get the location and validate it
        $location = Location::getByID($post_data['location']);
        if ($location === null) {
            throw new ValidationException('Invalid Location ID');
        }

        // Set the values
        foreach ($allowed_fields as $field) {
            if (!empty($post_data['new_location'][$field])) {
                $location->$field = $post_data['new_location'][$field];
            }
        }

        // If the user was not set or was set to the current user then allow for updates to user
        if (!isset($location->user_id) || $location->user_id === $user->uid) {
            // Update the user or un-save it if the user was removed
            if (array_key_exists('location_save', $post_data) && $post_data['location_save'] == 'on') {
                $location->user_id = $user->uid;
            } else {
                $location->user_id = null;
            }
        }

        // If location is saved to calendar then set the calendar id
        if (array_key_exists('location_save_calendar', $post_data) && $post_data['location_save_calendar'] == 'on') {
            $location->calendar_id = $calendar->id;
        } else {
            $location->calendar_id = null;
        }

        $location->update();

        return $location;
    }

    // Get the users locations
    public static function getUserLocations()
    {
        $user = Auth::getCurrentUser();
        return new Locations(array('user_id' => $user->uid));
    }

    // Get the locations from the calendar id
    public static function getCalendarLocations($calendar_id)
    {
        return new Locations(array('calendar_id' => $calendar_id));
    }

    // Get the standard locations
    public static function getStandardLocations($display_order)
    {
        return new Locations(array(
            'standard' => true,
            'display_order' => $display_order,
        ));
    }
}
