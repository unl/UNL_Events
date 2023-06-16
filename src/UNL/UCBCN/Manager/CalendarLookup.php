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

    public function getUsers()
    {
        if (!isset($this->calendar) || $this->calendar === false) {
            return array();
        }

        return $this->calendar->getUsers();
    }

    public function getUserPermissions(string $user_id)
    {
        if (!isset($this->calendar) || $this->calendar === false) {
            return array();
        }

        $options = array(
            'user_uid' => $user_id,
            'calendar_id' => $this->calendar->id,
        );

        return new Permissions($options);
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->post = $post;

        $this->calendar = Calendar::getByShortName($this->post['lookupTerm']);

        if ($this->calendar === false) {
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Calendar Not Found', 'We could not find a calendar matching your search.');
        }
    
        //redirect
        return null;
    }
}
