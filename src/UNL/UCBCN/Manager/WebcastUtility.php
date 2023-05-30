<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Webcast as Webcast;
use UNL\UCBCN\Webcasts;

class WebcastUtility
{
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
