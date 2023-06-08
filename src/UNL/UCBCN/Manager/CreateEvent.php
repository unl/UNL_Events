<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Manager\EventForm as EventForm;
use UNL\UCBCN as BaseUCBCN;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Audience;
use UNL\UCBCN\Event\Occurrence;

class CreateEvent extends EventForm
{
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->mode = self::MODE_CREATE;
        $this->event = new Event;
    }

    public function handlePost(array $get, array $post, array $files)
    {
        try {
            $new_event = $this->createEvent($post, $files);
        } catch (ValidationException $e) {
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t create your event', $e->getMessage());
            throw $e;
        }
        $this->flashNotice(
            parent::NOTICE_LEVEL_SUCCESS,
            'Event Created',
            'Your event "' . $new_event->title . '" has been created.'
        );

        # redirect
        return $this->calendar->getManageURL(TRUE);
    }

    private function validateEventData($post_data, $files)
    {
        # title, start date, location are required
        if (empty($post_data['title']) || empty($post_data['start_date'])) {
            throw new ValidationException(
                '<a href="#title">Title</a>, and <a href="#start-date">start date</a> are required.'
            );
        }

        # if we are sending this to UNL Main Calendar, description and contact info must be given
        if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
            if (empty($post_data['description']) || empty($post_data['contact_name'])) {
                throw new ValidationException(
                    '<a href="#contact-name">Contact name</a> and <a href="#description">description</a>' .
                    ' are required to recommend to UNL Main Calendar.'
                );
            }
        }

        # timezone must be valid
        if (empty($post_data['timezone']) || !(in_array($post_data['timezone'], BaseUCBCN::getTimezoneOptions()))) {
            throw new ValidationException('The timezone is invalid.');
        }

        # end date must be after start date
        $start_date = $this->calculateDate($post_data['start_date'],
            $post_data['start_time_hour'], $post_data['start_time_minute'],
            $post_data['start_time_am_pm']);

        $end_date = $this->calculateDate($post_data['end_date'],
            $post_data['end_time_hour'], $post_data['end_time_minute'],
            $post_data['end_time_am_pm']);

        if ($start_date > $end_date) {
            throw new ValidationException(
                'Your <a href="#end-date">end date/time</a>' .
                ' must be on or after the <a href="#start-date">start date/time</a>.'
            );
        }

        # Validate Recurring Event (if applicable)
        $this->validateRecurringEvent($post_data, $start_date, $end_date);

        // If there is a physical location make sure these are set
        if (isset($post_data['physical_location_check']) && $post_data['physical_location_check'] == '1') {
            if ($post_data['location'] == 'new' && empty($post_data['new_location']['name'])) {
                throw new ValidationException('You must give your new location a <a href="#location-name">name</a>.');
            }

            if ($post_data['location'] == 'new' && empty($post_data['new_location']['streetaddress1'])) {
                throw new ValidationException(
                    'You must give your new location an <a href=\"#location-address-1\">address</a>.'
                );
            }

            if ($post_data['location'] == 'new' && empty($post_data['new_location']['city'])) {
                throw new ValidationException('You must give your new location a <a href=\"#location-city\">city</a>.');
            }

            if ($post_data['location'] == 'new' && empty($post_data['new_location']['state'])) {
                throw new ValidationException(
                    'You must give your new location a <a href=\"#location-state\">state</a>.'
                );
            }

            if ($post_data['location'] == 'new' && empty($post_data['new_location']['zip'])) {
                throw new ValidationException('You must give your new location a <a href=\"#location-zip\">zip</a>.');
            }

            if ($post_data['location'] == 'new' && !empty($post_data['new_location']['webpageurl']) &&
                !filter_var($post_data['new_location']['webpageurl'], FILTER_VALIDATE_URL)) {
                throw new ValidationException('<a href=\"#location-webpage\">Location URL</a> is not a valid URL.');
            }
        }

        // If there is a virtual location make sure these are set
        if (isset($post_data['virtual_location_check']) && $post_data['virtual_location_check'] == '1') {
            if ($post_data['v_location'] == 'new' && empty($post_data['new_v_location']['title'])) {
                throw new ValidationException(
                    'You must give your new virtual location a <a href=\"#new-v-location-name\">name</a>.'
                );
            }

            if ($post_data['v_location'] == 'new' && empty($post_data['new_v_location']['url'])) {
                throw new ValidationException(
                    'You must give your new virtual location a <a href=\"#new-v-location-url\">URL</a>.'
                );
            } elseif ($post_data['v_location'] == 'new' &&
                !empty($post_data['new_v_location']['url']) &&
                !filter_var($post_data['new_v_location']['url'], FILTER_VALIDATE_URL)) {
                throw new ValidationException(
                    '<a href=\"#new-v-location-url\">Virtual Location URL</a> is not a valid URL.'
                );
            }
        }

        # website must be a valid url
        if (!empty($post_data['website']) && !filter_var($post_data['website'], FILTER_VALIDATE_URL)) {
            throw new ValidationException('Event Website must be a valid URL.');
        }

        # send to main is required
        if (empty($post_data['send_to_main'])) {
            throw new ValidationException('<a href="send_to_main">Consider for main calendar</a> is required.');
        }

        if (!empty($post_data['contact_type']) &&
            $post_data['contact_type'] !== "person" &&
            $post_data['contact_type'] !== "organization"
        ) {
            throw new ValidationException('<a href="#contact-type">Contact Type</a> must be person or organization.');
        }

        # website must be a valid url
        if (!empty($post_data['contact_website']) && !filter_var($post_data['contact_website'], FILTER_VALIDATE_URL)) {
            throw new ValidationException('Contact Website must be a valid URL.');
        }

        # Validate Image
        $this->validateEventImage($post_data, $files);
    }

    private function createEvent($post_data, $files)
    {
        $user = Auth::getCurrentUser();

        # tricky: if end date is empty, we want that to be the same as the start date
        # if the end time is also empty, then be sure to set the am/pm appropriately
        if (empty($post_data['end_date'])) {
            $post_data['end_date'] = $post_data['start_date'];
        }
        if (empty($post_data['end_time_hour']) && empty($post_data['end_time_minute'])) {
            $post_data['end_time_hour'] = $post_data['start_time_hour'];
            $post_data['end_time_minute'] = $post_data['start_time_minute'];
            $post_data['end_time_am_pm'] = $post_data['start_time_am_pm'];
        }

        # by setting and then validating, we allow the event on the form to have the entered data
        # so if the validation fails, the form shows with the entered data
        $this->setEventData($post_data, $files);
        $this->validateEventData($post_data, $files);

        $result = $this->event->insert($this->calendar, 'create event form');

        # add the event type record
        $event_has_type = new EventType;
        $event_has_type->event_id = $this->event->id;
        $event_has_type->eventtype_id = $post_data['type'];

        $event_has_type->insert();

        // Insert the audience if it is set
        $all_audiences = $this->getAudiences();
        foreach ($all_audiences as $current_audience) {
            $target_audience_id = 'target-audience-' . $current_audience->id;
            if (isset($post_data[$target_audience_id]) && $post_data[$target_audience_id] === $current_audience->id) {
                $event_targets_audience = new Audience;
                $event_targets_audience->event_id = $this->event->id;
                $event_targets_audience->audience_id = $post_data[$target_audience_id];

                $event_targets_audience->insert();
            }
        }

        # add the event date time record
        $event_datetime = new Occurrence;
        $event_datetime->event_id = $this->event->id;
        $event_datetime->canceled = 0;

        // check if physical location has been added
        if ($post_data['physical_location_check'] == "1") {

            // if a physical location is there then create a new one or set it to the selected one
            if ($post_data['location'] == 'new') {
                # create a new location
                $location = LocationUtility::addLocation($post_data, $user, $this->calendar);
                $event_datetime->location_id = $location->id;
            } else {
                $event_datetime->location_id = $post_data['location'];
            }

            // Set other location related fields
            $event_datetime->room = $post_data['room'];
            $event_datetime->directions = $post_data['directions'];
            $event_datetime->location_additionalpublicinfo = $post_data['l_additional_public_info'];
        }

        // check if physical location has been added
        if ($post_data['virtual_location_check'] == "1") {

            // if a virtual location is there then create a new one or set it to the selected one
            if ($post_data['v_location'] == 'new') {
                # create a new location
                $webcast = WebcastUtility::addWebcast($post_data, $user, $this->calendar);
                $event_datetime->webcast_id = $webcast->id;
            } else {
                $event_datetime->webcast_id = $post_data['v_location'];
            }

            // Set other webcast related fields
            $event_datetime->webcast_additionalpublicinfo = $post_data['v_additional_public_info'];
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
        $event_datetime->timezone = $post_data['timezone'];

        $event_datetime->additionalpublicinfo = $post_data['additional_public_info'];

        $event_datetime->insert();

        if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
            $this->event->considerForMainCalendar();
        }

        return $this->event;
    }
}
