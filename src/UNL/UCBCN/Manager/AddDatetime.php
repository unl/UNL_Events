<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Event;
use UNL\UCBCN\Locations;
use UNL\UCBCN\Event\Occurrence;

class AddDatetime implements PostHandlerInterface 
{
	public $options = array();
    public $calendar;
    public $event;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }
    }

    public function getUserLocations()
    {
        $user = Auth::getCurrentUser();
        return new Locations(array('user_id' => $user->uid));
    }
    
    public function getStandardLocations($display_order)
    {
        return new Locations(array(
            'standard'      => true,
            'display_order' => $display_order,
        ));
    }

    private function calculateDate($date, $hour, $minute, $am_or_pm)
    {
        $date = strtotime($date);
        # add hours correctly
        # TODO: handle when this is not entered
        $hours_to_add = (int)($hour) % 12;
        if ($am_or_pm == 'pm') {
            $hours_to_add += 12;
        }
        $date += $hours_to_add * 60 * 60 + (int)($minute) * 60;
        return date('Y-m-d H:i:s', $date);
    }

    public function handlePost(array $get, array $post, array $files)
    {
    	$this->addDatetime($post);
        return $this->event->getEditURL($this->calendar);
    }

    public function addDatetime($post_data) 
    {
        $user = Auth::getCurrentUser();

        # add the event date time record
        $event_datetime = new Occurrence;
        $event_datetime->event_id = $this->event->id;

        # check if this is to use a new location
        if ($post_data['location'] == 'new') {
            # create a new location
            $location = $this->addLocation($post_data, $user);
            $event_datetime->location_id = $location->id;
        } else {
            $event_datetime->location_id = $post_data['location'];
        }

        # set the start date and end date
        $event_datetime->starttime = $this->calculateDate($post_data['start_date'], 
            $post_data['start_time_hour'], $post_data['start_time_minute'], 
            $post_data['start_time_am_pm']);

        $event_datetime->endtime = $this->calculateDate($post_data['end_date'], 
            $post_data['end_time_hour'], $post_data['end_time_minute'], 
            $post_data['end_time_am_pm']);

        
        if (array_key_exists('recurring', $post_data) && $post_data['recurring'] == 'on') {
            $event_datetime->recurringtype = $post_data['recurring_type'];
            $event_datetime->recurs_until = $this->calculateDate(
                $post_data['recurs_until_date'], $post_data['recurs_until_time_hour'], 
                $post_data['recurs_until_time_minute'], 
                $post_data['recurs_until_time_am_pm']);
            if ($event_datetime->recurringtype == 'monthly') {
                $event_datetime->rectypemonth = $post_data['recurring_monthly_type'];
            }
        } else {
            $event_datetime->recurringtype = 'none';
        }
        $event_datetime->room = $post_data['room'];
        $event_datetime->directions = $post_data['directions'];
        $event_datetime->additionalpublicinfo = $post_data['additional_public_info'];

        $event_datetime->insert();

        return $event_datetime;
    }
}