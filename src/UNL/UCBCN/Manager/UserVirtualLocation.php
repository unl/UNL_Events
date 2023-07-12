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

        return $user->hasPermission($edit_permission->id, $calendar_id)
            && $user->hasPermission($create_permission->id, $calendar_id);
    }

    public function handlePost(array $get, array $post, array $files)
    {
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

        //redirect
        return Controller::getUserVirtualLocationURL();
    }

    private function createWebcast(array $post_data)
    {
        $user = Auth::getCurrentUser();
        $post_data['v_location_save'] = 'on';

        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['v_location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        $this->validateWebcast($post_data);

        WebcastUtility::addWebcast($post_data, $user, $calendar);
    }

    private function updateWebcast(array $post_data)
    {
        $user = Auth::getCurrentUser();
        $post_data['v_location_save'] = 'on';

        $calendar = null;
        if (isset($post_data['calendar_id']) && is_numeric($post_data['calendar_id'])) {
            $post_data['v_location_save_calendar'] = 'on';
            $calendar = Calendar::getByID($post_data['calendar_id']);
            if (!$this->userHasAccessToCalendar($post_data['calendar_id'])) {
                throw new ValidationException('You do not have access to that calendar.');
            }
        }

        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Virtual Location');
        }

        if (
            !(isset($webcast->user_id) && $webcast->user_id === $user->uid) &&
            !(isset($webcast->calendar_id) && $this->userHasAccessToCalendar($webcast->calendar_id))
        ) {
            throw new ValidationException('You do not have access to modify that virtual location.');
        }

        if (!empty($post_data['v_location']) && $post_data['v_location'] === "New") {
            throw new ValidationException('Missing Virtual Location To Update.');
        }

        $this->validateWebcast($post_data);

        try {
            WebcastUtility::updateWebcast($post_data, $user, $calendar);
        } catch(ValidationException $e) {
            throw new ValidationException('Error Updating Virtual Location');
        }
    }

    private function detachWebcast(array $post_data)
    {
        if (!empty($post_data['v_location']) && $post_data['v_location'] === "New") {
            throw new ValidationException('Missing Location To Detach');
        }

        $webcast = Webcast::getByID($post_data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Virtual Location ID');
        }

        $webcast->user_id = null;
        $webcast->update();
    }

    private function validateWebcast(array $post_data)
    {
        $validate_data = WebcastUtility::validateWebcast($post_data);
        if (!$validate_data['valid']) {
            throw new ValidationException($validate_data['message']);
        }
    }
}
