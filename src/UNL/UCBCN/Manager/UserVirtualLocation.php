<?php
namespace UNL\UCBCN\Manager;

use Exception;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Location as Location;

class UserVirtualLocation extends PostHandler
{
    public $options = array();
    public $post;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    public function getUserWebcasts() 
    {
        return WebcastUtility::getUserWebcasts();
    }

    public function getUserCalendars() 
    {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }

    public function userHasAccessToCalendar(string $calendar_id)
    {
        $user = Auth::getCurrentUser();

        $edit_permission = Permission::getByName('Event Edit');
        $create_permission = Permission::getByName('Event Create');

        return $user->hasPermission($edit_permission->id, $calendar_id) && $user->hasPermission($create_permission->id, $calendar_id);
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
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Created', 'Your Location has been created.');
                break;
            case "put":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Updated', 'Your Location has been updated.');
                break;
            case "delete":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Detached', 'Your Location has been detached from you.');
                break;
        }

        //redirect
        return Controller::getUserLocationURL();
    }

    private function create_location(array $post_data)
    {
        $user = Auth::getCurrentUser();
        $post_data['location_save'] = 'on';

        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        $this->validateLocation($post_data);

        LocationUtility::addLocation($post_data, $user, $calendar);
    }

    private function update_location(array $post_data)
    {
        $user = Auth::getCurrentUser();
        $post_data['location_save'] = 'on';

        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        if (!empty($post_data['location']) && $post_data['location'] === "New") {
            throw new ValidationException('Missing Location To Update.');
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

        $location->user_id = null;
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
