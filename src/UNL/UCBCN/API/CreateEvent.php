<?php
namespace UNL\UCBCN\API;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Event;
use UNL\UCBCN\Permission;

use UNL\UCBCN\Manager\Auth;

class CreateEvent
{
    public $options = array();
    public $calendar;
    public $event;

    public $result;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

        if ($this->calendar === false) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_CREATE_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to create an event on this calendar.", 403);
        }

        $this->event = new Event;
    }

    public function handleGet()
    {
        http_response_code('503');
        echo 'This API endpoint has been shutdown, please use the new API.';
        exit;
    }

    public function handlePost()
    {
        http_response_code('503');
        echo 'This API endpoint has been shutdown, please use the new API.';
        exit;
    }
}