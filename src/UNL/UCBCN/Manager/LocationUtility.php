<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Location as Location;
use UNL\UCBCN\Locations;

class LocationUtility
{
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
            !filter_var($post_data['webpageurl'], FILTER_VALIDATE_URL)
        ) {
            $outputMessage = '<a href="#location-webpage">Location URL</a> is not a valid URL.';
            $isValid = false;
        }

        if ($isValid && !empty($post_data['new_location']['mapurl']) &&
            !filter_var($post_data['mapurl'], FILTER_VALIDATE_URL)
        ) {
            $outputMessage = '<a href="#location-mapurl">Location Map URL</a> is not a valid URL.';
            $isValid = false;
        }

        return array("valid" => $isValid, "message" => $outputMessage);
    }

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

        $location = new Location;

        foreach ($allowed_fields as $field) {
            $value = $post_data['new_location'][$field];
            if (!empty($value)) {
                $location->$field = $value;
            }
        }

        if (array_key_exists('location_save', $post_data) && $post_data['location_save'] == 'on') {
            $location->user_id = $user->uid;
        }

        if (array_key_exists('location_save_calendar', $post_data) && $post_data['location_save_calendar'] == 'on') {
            $location->calendar_id = $calendar->id;
        }
        
        $location->standard = 0;

        $location->insert();

        return $location;
    }

    public static function getUserLocations()
    {
        $user = Auth::getCurrentUser();
        return new Locations(array('user_id' => $user->uid));
    }

    public static function getCalendarLocations($calendar_id)
    {
        return new Locations(array('calendar_id' => $calendar_id));
    }

    public static function getStandardLocations($display_order)
    {
        return new Locations(array(
            'standard' => true,
            'display_order' => $display_order,
        ));
    }
}
