<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Webcast as Webcast;
use UNL\UCBCN\Webcasts;

class WebcastUtility
{
    // Function for validating a virtual location's data
    public static function validateWebcast(array $post_data): array {
        $outputMessage = "";
        $isValid = true;

        if (empty($post_data['new_v_location']['title'])) {
            $outputMessage = 'You must give your new virtual location a <a href="#new-v-location-name">name</a>.';
            $isValid = false;
        }

        if ($isValid && empty($post_data['new_v_location']['url'])) {
            $outputMessage = 'You must give your new virtual location a <a href="#new-v-location-url">URL</a>.';
            $isValid = false;
        }
        
        if ($isValid && !empty($post_data['new_v_location']['url']) &&
            !filter_var($post_data['new_v_location']['url'], FILTER_VALIDATE_URL)
        ) {
            $outputMessage = '<a href="#new-v-location-url">Virtual Location URL</a> is not a valid URL.';
            $isValid = false;
        }

        return array("valid" => $isValid, "message" => $outputMessage);
    }

    // Function for creating a new virtual location
    public static function addWebcast(array $post_data, $user, $calendar)
    {
        // These need to match webcast table
        $allowed_fields = array(
            'title',
            'url',
            'additionalinfo',
        );

        // creates a new webcast and fills the values 
        $webcast = new Webcast;
        foreach ($allowed_fields as $field) {
            $value = $post_data['new_v_location'][$field];
            if (!empty($value)) {
                $webcast->$field = $value;
            }
        }

        // If webcast is saved to user set the user uid
        if (array_key_exists('v_location_save', $post_data) && $post_data['v_location_save'] == 'on') {
            $webcast->user_id = $user->uid;
        }

        // If webcast is saved to calendar then set the calendar id
        if (array_key_exists('v_location_save_calendar', $post_data) &&
            $post_data['v_location_save_calendar'] == 'on') {
            $webcast->calendar_id = $calendar->id;
        }

        $webcast->insert();

        return $webcast;
    }

    // Function for updating an existing virtual location
    public static function updateWebcast(array $post_data, $user, $calendar)
    {
        // These need to match webcast table
        $allowed_fields = array(
            'title',
            'url',
            'additionalinfo',
        );

        // Get the virtual location and validate it
        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Location ID');
        }

        // Set the values
        foreach ($allowed_fields as $field) {
            $value = $post_data['new_v_location'][$field];
            if (!empty($value)) {
                $webcast->$field = $value;
            }
        }

        // If the user was not set or was set to the current user then allow for updates to user
        if (!isset($webcast->user_id) || $webcast->user_id === $user->uid) {
            // Update the user or un-save it if the user was removed
            if (array_key_exists('v_location_save', $post_data) && $post_data['v_location_save'] == 'on') {
                $webcast->user_id = $user->uid;
            } else {
                $webcast->user_id = null;
            }
        }

        // If virtual location is saved to calendar then set the calendar id
        if (array_key_exists('v_location_save_calendar', $post_data) &&
            $post_data['v_location_save_calendar'] == 'on') {
            $webcast->calendar_id = $calendar->id;
        } else {
            $webcast->calendar_id = null;
        }

        $webcast->update();

        return $webcast;
    }

     // Get the users virtual locations
    public static function getUserWebcasts()
    {
        $user = Auth::getCurrentUser();
        return new Webcasts(array('user_id' => $user->uid));
    }

    // Get the virtual locations from the calendar id
    public static function getCalendarWebcasts($calendar_id)
    {
        return new Webcasts(array('calendar_id' => $calendar_id));
    }
}
