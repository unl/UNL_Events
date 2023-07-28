<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as Calendar;
use UNL\UCBCN\Permissions;

class CalendarLookup extends PostHandler
{
    public $options = array();
    public $post;
    public $calendar;
    

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    // Gets a list of the users on a calendar
    public function getUsers()
    {
        // Checks if the calendar is set
        if (!isset($this->calendar) || $this->calendar === false) {
            return array();
        }

        return $this->calendar->getUsers();
    }

    // Gets a users permissions based on their ID
    public function getUserPermissions(string $user_id): Permissions
    {
        // Checks if the calendar is set
        if (!isset($this->calendar) || $this->calendar === false) {
            return array();
        }

        // Returns a permissions list
        $options = array(
            'user_uid' => $user_id,
            'calendar_id' => $this->calendar->id,
        );
        return new Permissions($options);
    }

    // Gets the calendar based on the shortname of the calendar
    public function handlePost(array $get, array $post, array $files)
    {
        // This value will be used for the input on the page
        $this->post = $post;

        // Tries to get the shortname, throws notice if not found
        $this->calendar = Calendar::getByShortName($this->post['lookupTerm']);
        if ($this->calendar === false) {
            $this->flashNotice(
                parent::NOTICE_LEVEL_ALERT,
                'Calendar Not Found',
                'We could not find a calendar matching your search.'
            );
        }
    
        // Prevents redirect
        return null;
    }
}
