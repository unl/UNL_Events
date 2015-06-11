<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Event;
use UNL\UCBCN\Locations;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\Event\RecurringDate;
use UNL\UCBCN\Event\RecurringDates;

class AddDatetime implements PostHandlerInterface 
{
	public $options = array();
    public $calendar;
    public $event;
    public $event_datetime;
    public $recurrence_id;
    public $original_event_datetime_id;

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

        if (array_key_exists('event_datetime_id', $this->options)) {
            # we are editing an existing datetime
            $this->event_datetime = Occurrence::getByID($this->options['event_datetime_id']);

            if ($this->event_datetime === FALSE) {
                throw new \Exception("That datetime could not be found", 404);
            }

            $this->original_event_datetime_id = $this->event_datetime->id;

            # now we check for if we are editing a specific recurrence
            if (array_key_exists('recurrence_id', $this->options)) {

                $recurrence = RecurringDate::getByEventDatetimeIDRecurrenceID($this->event_datetime->id, $this->options['recurrence_id']);

                if ($recurrence === FALSE) {
                    throw new \Exception("That recurrence could not be found", 404);
                }

                $this->recurrence_id = $recurrence->recurrence_id;

                $temp_event_datetime = $this->event_datetime;
                $temp_event_datetime->id = NULL;

                # set the start and end time based on the recurring date record
                $event_length = strtotime($temp_event_datetime->endtime) - strtotime($temp_event_datetime->starttime);
                $temp_event_datetime->starttime = $recurrence->recurringdate . ' ' . date('H:i:s', strtotime($temp_event_datetime->starttime));
                $temp_event_datetime->endtime = date('Y-m-d H:i:s', strtotime($temp_event_datetime->starttime) + $event_length);

                $temp_event_datetime->recurringtype = 'none';
                $temp_event_datetime->rectypemonth = NULL;
                $temp_event_datetime->recurs_until = NULL;

                $this->event_datetime = $temp_event_datetime;
            }

        } else {
            # we are adding a new datetime
            $this->event_datetime = new Occurrence;
        }
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->editDatetime($this->event_datetime, $post);

        # if we are editing a single recurrence, we need to unlink the current one in the DB
        # set unlinked on all recurring dates with the recurrence id and event id
        if (array_key_exists('recurrence_id', $this->options)) {
            $recurring_dates = new RecurringDates(array(
                'event_datetime_id' => $this->original_event_datetime_id,
                'recurrence_id' => $this->options['recurrence_id']
            ));

            foreach ($recurring_dates as $recurring_date) {
                $recurring_date->unlinked = 1;
                $recurring_date->save();
            }
        }

        return $this->event->getEditURL($this->calendar);
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

     /**
     * Add a location
     * 
     * @param array $post_data
     * @return Location
     */
    protected function addLocation(array $post_data, $user)
    {
        $allowed_fields = array(
            'name',
            'streetaddress1',
            'streetaddress2',
            'room',
            'city',
            'state',
            'zip',
            'mapurl',
            'webpageurl',
            'hours',
            'directions',
            'additionalpublicinfo',
            'type',
            'phone',
        );

        $location = new Location;

        foreach ($allowed_fields as $field) {
            $location->$field = $post_data['new_location'][$field];
        }

        if (array_key_exists('location_save', $post_data) && $post_data['location_save'] == 'on') {
            $location->user_id = $user->uid;
        }
        $location->standard = 0;

        $location->insert();
        
        return $location;
    }

    public function editDatetime($event_datetime, $post_data) 
    {
        $user = Auth::getCurrentUser();
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
                $post_data['recurs_until_date'], 11, 59, 'PM');
            if ($event_datetime->recurringtype == 'date' || 
                $event_datetime->recurringtype == 'lastday' || 
                $event_datetime->recurringtype == 'first' ||
                $event_datetime->recurringtype == 'second' ||
                $event_datetime->recurringtype == 'third'|| 
                $event_datetime->recurringtype == 'fourth' ||
                $event_datetime->recurringtype == 'last') {
                    $event_datetime->rectypemonth = $event_datetime->recurringtype;
                    $event_datetime->recurringtype = 'monthly';
            }
        } else {
            $event_datetime->recurringtype = 'none';
        }
        $event_datetime->room = $post_data['room'];
        $event_datetime->directions = $post_data['directions'];
        $event_datetime->additionalpublicinfo = $post_data['additional_public_info'];

        $event_datetime->save();

        return $event_datetime;
    }
}