<?php
namespace UNL\UCBCN\Manager;

use Exception;
use UNL\UCBCN\Webcast as Webcast;
use UNL\UCBCN\Webcasts;

class WebcastUtility
{
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

    public static function addWebcast(array $post_data, $user, $calendar)
    {
        // These need to match webcast table
        $allowed_fields = array(
            'title',
            'url',
            'additionalinfo',
        );

        $webcast = new Webcast;

        foreach ($allowed_fields as $field) {
            $value = $post_data['new_v_location'][$field];
            if (!empty($value)) {
                $webcast->$field = $value;
            }
        }

        if (array_key_exists('v_location_save', $post_data) && $post_data['v_location_save'] == 'on') {
            $webcast->user_id = $user->uid;
        }

        if (array_key_exists('v_location_save_calendar', $post_data) &&
            $post_data['v_location_save_calendar'] == 'on') {
            $webcast->calendar_id = $calendar->id;
        }

        $webcast->insert();

        return $webcast;
    }

    public static function updateWebcast(array $post_data, $user, $calendar)
    {
        // These need to match webcast table
        $allowed_fields = array(
            'title',
            'url',
            'additionalinfo',
        );

        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new Exception('Invalid Location ID');
        }


        foreach ($allowed_fields as $field) {
            $value = $post_data['new_v_location'][$field];
            if (!empty($value)) {
                $webcast->$field = $value;
            }
        }

        if (!isset($webcast->user_id) || $webcast->user_id === $user->uid) {
            if (array_key_exists('v_location_save', $post_data) && $post_data['v_location_save'] == 'on') {
                $webcast->user_id = $user->uid;
            } else {
                $webcast->user_id = null;
            }
        }

        if (array_key_exists('v_location_save_calendar', $post_data) &&
            $post_data['v_location_save_calendar'] == 'on') {
            $webcast->calendar_id = $calendar->id;
        } else {
            $webcast->calendar_id = null;
        }

        $webcast->update();

        return $webcast;
    }

    public static function getUserWebcasts()
    {
        $user = Auth::getCurrentUser();
        return new Webcasts(array('user_id' => $user->uid));
    }

    public static function getCalendarWebcasts($calendar_id)
    {
        return new Webcasts(array('calendar_id' => $calendar_id));
    }
}
