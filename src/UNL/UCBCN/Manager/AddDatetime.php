<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN as BaseUCBCN;
use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Event;
use UNL\UCBCN\Locations;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\Event\RecurringDate;
use UNL\UCBCN\Event\RecurringDates;

class AddDatetime extends PostHandler
{
	public $options = array();
    public $calendar;
    public $event;
    public $event_datetime;
    public $recurrence_id;
    public $original_event_datetime_id;
    public $post;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::EVENT_EDIT_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit events on this calendar.", 403);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        if (!$this->event->userCanEdit()) {
            throw new \Exception("You do not have permission to edit this event.", 403);
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

                $recurrence = RecurringDate::getByEventDatetimeIDRecurrenceID(
                    $this->event_datetime->id,
                    $this->options['recurrence_id']
                );

                if ($recurrence === FALSE) {
                    throw new \Exception("That recurrence could not be found", 404);
                }

                $this->recurrence_id = $recurrence->recurrence_id;

                $temp_event_datetime = $this->event_datetime;
                $temp_event_datetime->id = NULL;

                # set the start and end time based on the recurring date record
                $event_length = strtotime($temp_event_datetime->endtime) - strtotime($temp_event_datetime->starttime);
                $temp_event_datetime->starttime = $recurrence->recurringdate .
                    ' ' .
                    date('H:i:s', strtotime($temp_event_datetime->starttime));
                $temp_event_datetime->endtime = date(
                    'Y-m-d H:i:s',
                    strtotime($temp_event_datetime->starttime) + $event_length
                );

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
        $this->post = $post;

        if (isset($post['toggle-cancel'])) {
            $this->processCancelToggle($post);
            die();
        } else {
            $this->processEdit($post);
            return $this->event->getEditURL($this->calendar);
        }
    }

    private function processCancelToggle($post) {
        $result = new \stdClass();
        $result->success = TRUE;
        $result->error = NULL;

        try {
            $canceled = $post['canceled'] === 'true' ? 1 : 0;
            if (array_key_exists('recurrence_id', $this->options)) {
                $recurring_dates = new RecurringDates(array(
                    'event_datetime_id' => $this->original_event_datetime_id,
                    'recurrence_id' => $this->options['recurrence_id']
                ));
                foreach ($recurring_dates as $recurring_date) {
                    $recurring_date->canceled = $canceled;
                    $result->canceled = $canceled;
                    $recurring_date->save();
                }
            } else {
                $this->event_datetime->canceled = $canceled;
                $result->canceled = $canceled;
                $this->event_datetime->save();
            }
        }  catch (\Exception $e) {
            $result->success = FALSE;
            $error = new \stdClass();
            $error->code = $e->getCode();
            $error->message = $e->getMessage();
            $result->error = $error;
        }

        // display result as json for ajax
        echo json_encode($result);
    }

    private function processEdit($post) {
	    $new = $this->event_datetime->id == NULL;
	    try {
		    $this->editDatetime($post);
	    } catch (ValidationException $e) {
		    $this->flashNotice(
                parent::NOTICE_LEVEL_ALERT,
                'Sorry! We couldn\'t ' .
                    ($new ? 'create' : 'update') .
                    ' this location/date/time',
                $e->getMessage()
            );
		    throw $e;
	    }

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
        if ($new) {
            $this->flashNotice(
                parent::NOTICE_LEVEL_SUCCESS,
                'Location/Date/Time Added',
                'Another location, date and time has been added.'
            );
        } else {
            $this->flashNotice(
                parent::NOTICE_LEVEL_SUCCESS,
                'Location/Date/Time Updated', '
                Your location, date and time has been updated.'
            );
        }
    }

    private function setDatetimeData($post_data)
    {
        # set the start date and end date
        $this->event_datetime->timezone = empty($post_data['timezone']) ?
            BaseUCBCN::$defaultTimezone : $post_data['timezone'];
        $this->event_datetime->starttime = $this->calculateDate($post_data['start_date'],
            $post_data['start_time_hour'], $post_data['start_time_minute'],
            $post_data['start_time_am_pm']);

        $this->event_datetime->endtime = $this->calculateDate($post_data['end_date'],
            $post_data['end_time_hour'], $post_data['end_time_minute'],
            $post_data['end_time_am_pm']);

        if (array_key_exists('recurring', $post_data) && $post_data['recurring'] == 'on') {
            $this->event_datetime->recurringtype = $post_data['recurring_type'];
            $this->event_datetime->recurs_until = $this->calculateDate(
                $post_data['recurs_until_date'], 11, 59, 'pm');
            if ($this->event_datetime->recurringtype == 'date' ||
                $this->event_datetime->recurringtype == 'lastday' ||
                $this->event_datetime->recurringtype == 'first' ||
                $this->event_datetime->recurringtype == 'second' ||
                $this->event_datetime->recurringtype == 'third'||
                $this->event_datetime->recurringtype == 'fourth' ||
                $this->event_datetime->recurringtype == 'last') {
                    $this->event_datetime->rectypemonth = $this->event_datetime->recurringtype;
                    $this->event_datetime->recurringtype = 'monthly';
            }
        } else {
            $this->event_datetime->recurringtype = 'none';
        }

        $this->event_datetime->additionalpublicinfo = $post_data['additional_public_info'];
    }

    private function validateDatetimeData($post_data)
    {
        # timezone must be valid
        if (empty($post_data['timezone']) || !(in_array($post_data['timezone'], BaseUCBCN::getTimezoneOptions()))) {
            throw new ValidationException('The timezone is invalid.');
        }

        # start date, location are required
        if (empty($post_data['start_date'])) {
            throw new ValidationException('<a href="#start-date">start date</a> are required.');
        }

        # end date must be after start date
        $start_date = $this->calculateDate($post_data['start_date'],
            $post_data['start_time_hour'], $post_data['start_time_minute'],
            $post_data['start_time_am_pm']);

        $end_date = $this->calculateDate($post_data['end_date'],
            $post_data['end_time_hour'], $post_data['end_time_minute'],
            $post_data['end_time_am_pm']);

        if ($start_date > $end_date) {
            throw new ValidationException('Your <a href="#end-date">end date/time</a> must be on or after the <a href="#start-date">start date/time</a>.');
        }

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
            } elseif ($post_data['v_location'] == 'new' && !empty($post_data['new_v_location']['url']) &&
                !filter_var($post_data['new_v_location']['url'], FILTER_VALIDATE_URL)) {
                throw new ValidationException(
                    '<a href=\"#new-v-location-url\">Virtual Location URL</a> is not a valid URL.'
                );
            }
        }

        # Validate Recurring Event (if applicable)
        $this->validateRecurringEvent($post_data, $start_date, $end_date);
    }

    public function editDatetime($post_data)
    {
        $user = Auth::getCurrentUser();

        # make a copy of the original event_datetime coming into this method
        # we'll need it to check if we have changed the date & it's recurring
        # to see if we need to revamp the recurrences
        $datetime_copy = clone $this->event_datetime;

        $this->event_datetime->event_id = $this->event->id;

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

        // Checks if we have a physical location
        if ($post_data['physical_location_check'] == '0') {

            // if not we check if we need to set the location to null and delete the old location
            if (isset($this->event_datetime->location_id) && !empty($this->event_datetime->location_id)) {
                $location_to_be_deleted = $this->event_datetime->getLocation();
                $this->event_datetime->location_id = null;
                if (isset($location_to_be_deleted) && !$location_to_be_deleted->isSavedOrStandard()) {
                    $location_to_be_deleted->delete();
                }
            }
        } else {
            // check if this is to use a new location
            if ($post_data['location'] == 'new') {
                // create a new location
                $location = LocationUtility::addLocation($post_data, $user, $this->calendar);
                $this->event_datetime->location_id = $location->id;
            } else {
                $this->event_datetime->location_id = $post_data['location'];
            }

            // set other location related fields
            $this->event_datetime->room = $post_data['room'];
            $this->event_datetime->directions = $post_data['directions'];
            $this->event_datetime->location_additionalpublicinfo = $post_data['l_additional_public_info'];
        }

         // Checks if we have a virtual location
        if ($post_data['virtual_location_check'] == '0') {

            // if not we check if we need to set the webcast to null and delete the old webcast
            if (isset($this->event_datetime->webcast_id) && !empty($this->event_datetime->webcast_id)) {
                $webcast_to_be_deleted = $this->event_datetime->getWebcast();
                $this->event_datetime->webcast_id = null;
                if (isset($webcast_to_be_deleted) && !$webcast_to_be_deleted->isSaved()) {
                    $webcast_to_be_deleted->delete();
                }
            }
        } else {
            // if a virtual location is there then create a new one or set it to the selected one
            if ($post_data['v_location'] == 'new') {
                // create a new location
                $webcast = WebcastUtility::addWebcast($post_data, $user, $this->calendar);
                $this->event_datetime->webcast_id = $webcast->id;
            } else {
                $this->event_datetime->webcast_id = $post_data['v_location'];
            }

            // set other webcast related fields
            $this->event_datetime->webcast_additionalpublicinfo = $post_data['v_additional_public_info'];
        }

        $this->setDatetimeData($post_data);
        $this->validateDatetimeData($post_data);

        if (!isset($this->event_datetime->canceled)) {
            $this->event_datetime->canceled = 0;
        }

        $this->event_datetime->save();

        # if we are editing a datetime, we need to check whether to revamp the recurrences
        if ($datetime_copy->id != NULL) {
            # if we are newly recurring
            if (!$datetime_copy->isRecurring() && $this->event_datetime->isRecurring()) {
                $this->event_datetime->insertRecurrences();
            # if we are removing recurring completely
            } elseif ($datetime_copy->isRecurring() && !$this->event_datetime->isRecurring()) {
                $this->event_datetime->deleteRecurrences();
            # if we are recurring before and after the change
            } elseif ($datetime_copy->isRecurring() && $this->event_datetime->isRecurring()) {
                # start time, end time, frequency and recurs until must all remain the same
                # or we wipe it and start over

                if ($datetime_copy->starttime != $this->event_datetime->starttime ||
                        $datetime_copy->endtime != $this->event_datetime->endtime ||
                        $datetime_copy->recurringtype != $this->event_datetime->recurringtype ||
                        $datetime_copy->rectypemonth != $this->event_datetime->rectypemonth ||
                        $datetime_copy->recurs_until != $this->event_datetime->recurs_until) {
                    $this->event_datetime->deleteRecurrences();
                    $this->event_datetime->insertRecurrences();
                }
            }
        }

        return $this->event_datetime;
    }


    /**
     * Get the original datetime unmodified.
     *
     * @return UNL\UCBCN\Event\Occurrence
     */
    public function getOriginalDatetime()
    {
        $temp_event_datetime = Occurrence::getByID($this->original_event_datetime_id);

        # now we check for if we are editing a specific recurrence
        if (isset($this->recurrence_id)) {
            $recurrence = RecurringDate::getByEventDatetimeIDRecurrenceID(
                $this->original_event_datetime_id,
                $this->recurrence_id
            );

            $temp_event_datetime->id = null;

            # set the start and end time based on the recurring date record
            $event_length = strtotime($temp_event_datetime->endtime) - strtotime($temp_event_datetime->starttime);
            $temp_event_datetime->starttime = $recurrence->recurringdate .
                ' ' . date('H:i:s', strtotime($temp_event_datetime->starttime));
            $temp_event_datetime->endtime = date(
                'Y-m-d H:i:s',
                strtotime($temp_event_datetime->starttime) + $event_length
            );

            $temp_event_datetime->recurringtype = 'none';
            $temp_event_datetime->rectypemonth = null;
            $temp_event_datetime->recurs_until = null;
        }

        return $temp_event_datetime;
    }
}
