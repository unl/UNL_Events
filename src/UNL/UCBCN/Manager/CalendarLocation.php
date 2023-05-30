<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN as BaseUCBCN;

class CalendarLocation extends PostHandler
{
    public $options = array();
    public $calendar;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        # check if we are looking to edit a calendar
        if (array_key_exists('calendar_shortname', $this->options)) {
            $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        # redirect
        return $this->calendar->getManageURL();
    }
}
