<?php
namespace UNL\UCBCN\Manager;

use Exception;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Location as Location;

class CalendarLocation extends PostHandler
{
    public $options = array();
    public $post;
    public $calendar;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortName($this->options['calendar_shortname']);
    }

    public function getCalendarLocations() 
    {
        return LocationUtility::getCalendarLocations($this->calendar->id);
    }

    public function getUserCalendars() 
    {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $method = $post['method'] ?? "";
        try {
            switch ($method) {
                case "post":
                    $this->create_location($post);
                    break;
                case "put":
                    $this->update_location($post);
                    break;
                case "delete":
                    $this->detach_location($post);
                    break;
                default: 
                    throw new ValidationException('Invalid Method');
            }

        } catch (ValidationException $e) {
            $this->post = $post;
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t create your event', $e->getMessage());
            throw $e;
        }

        switch ($method) {
            case "post":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Created', 'Your calendar\'s location has been created.');
                break;
            case "put":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Updated', 'Your calendar\'s location has been updated.');
                break;
            case "delete":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Detached', 'The location has been detached from your calendar.');
                break;
        }

        //redirect
        return $this->calendar->getLocationURL();
    }

    private function create_location(array $post_data)
    {
        $user = Auth::getCurrentUser();

        $post_data['location_save_calendar'] = 'on';
        $calendar = $this->calendar;

        $this->validateLocation($post_data);

        LocationUtility::addLocation($post_data, $user, $calendar);
    }

    private function update_location(array $post_data)
    {
        $user = Auth::getCurrentUser();

        $post_data['location_save_calendar'] = 'on';
        $calendar = $this->calendar;

        if (!empty($post_data['location']) && $post_data['location'] === "New") {
            throw new ValidationException('Missing Location To Update');
        }

        $this->validateLocation($post_data);

        try {
            LocationUtility::updateLocation($post_data, $user, $calendar);
        } catch(Exception $e) {
            throw new ValidationException('Error Updating Location');
        }
    }

    private function detach_location(array $post_data)
    {
        if (!empty($post_data['location']) && $post_data['location'] === "New") {
            throw new ValidationException('Missing Location To Detach');
        }

        $location = Location::getByID($post_data['location']);
        if ($location === null) {
            throw new ValidationException('Invalid Location ID');
        }

        $location->calendar_id = null;
        $location->update();
    }

    private function validateLocation(array $post_data)
    {
        $validate_data = LocationUtility::validateLocation($post_data);
        if (!$validate_data['valid']) {
            throw new ValidationException($validate_data['message']);
        }
    }
}
