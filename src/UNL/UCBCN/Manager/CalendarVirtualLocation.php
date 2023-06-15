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
    }

    public function getCalendarWebcasts()
    {
        return WebcastUtility::getCalendarWebcasts($this->calendar->id);
    }

    public function getCurrentUser()
    {
        $user = Auth::getCurrentUser();

        return $user->uid;
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
                    $this->create_webcast($post);
                    break;
                case "put":
                    $this->update_webcast($post);
                    break;
                case "delete":
                    $this->detach_webcast($post);
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
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Virtual Location Created', 'Your calendar\'s virtual location has been created.');
                break;
            case "put":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Virtual Location Updated', 'Your calendar\'s virtual location has been updated.');
                break;
            case "delete":
                $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Virtual Location Detached', 'The virtual location has been detached from your calendar.');
                break;
        }

        //redirect
        return $this->calendar->getVirtualLocationURL();
    }

    private function create_webcast(array $post_data)
    {
        $user = Auth::getCurrentUser();

        $post_data['v_location_save_calendar'] = 'on';
        $calendar = $this->calendar;

        $this->validateLocation($post_data);

        WebcastUtility::addWebcast($post_data, $user, $calendar);
    }

    private function update_webcast(array $post_data)
    {
        $user = Auth::getCurrentUser();

        $post_data['v_location_save_calendar'] = 'on';
        $calendar = $this->calendar;

        if (!empty($post_data['v_location']) && $post_data['v_location'] === "New") {
            throw new ValidationException('Missing Virtual Location To Update');
        }

        $this->validateLocation($post_data);

        try {
            WebcastUtility::updateWebcast($post_data, $user, $calendar);
        } catch(Exception $e) {
            throw new ValidationException('Error Updating Virtual Location');
        }
    }

    private function detach_webcast(array $post_data)
    {
        if (!empty($post_data['v_location']) && $post_data['v_location'] === "New") {
            throw new ValidationException('Missing Virtual Location To Detach');
        }

        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Virtual Location ID');
        }

        $webcast->calendar_id = null;
        $webcast->update();
    }

    private function validateLocation(array $post_data)
    {
        $validate_data = WebcastUtility::validateWebcast($post_data);
        if (!$validate_data['valid']) {
            throw new ValidationException($validate_data['message']);
        }
    }
}
