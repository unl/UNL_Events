<?php
namespace UNL\UCBCN\Manager;

class UserLocation extends PostHandler
{
    public $options = array();
    public $post;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    public function getUserLocations() 
    {
        return LocationUtility::getUserLocations();
    }

    public function getUserCalendars() 
    {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }

    public function userHasAccessToCalendar(string $calendar_id)
    {
        $user = Auth::getCurrentUser();

        // $pending_permission = Permission::getByName('Event Send Event to Pending Queue');
        // $posted_permission = Permission::getByName('Event Post');

        return $user->hasPermission(5, $calendar_id) && $user->hasPermission(25, $calendar_id);
    }

    public function handlePost(array $get, array $post, array $files)
    {
        
        try {
            throw new ValidationException('<pre>' . print_r($post, true) . '</pre>');
        } catch (ValidationException $e) {
            $this->post = $post;
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t create your event', $e->getMessage());
            throw $e;
        }
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Location Updated', 'Your Location has been updated.');

        //redirect
        return Controller::getUserLocationURL();
    }
}
