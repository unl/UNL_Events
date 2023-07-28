<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Event;
use UNL\UCBCN\Calendar\Event as CalendarEvent;
use UNL\UCBCN\Event\Occurrence as Occurrence;
use UNL\UCBCN\Event\Occurrences as Occurrences;
use UNL\UCBCN\Event\RecurringDate;
use UNL\UCBCN\Manager\CreateEvent;
use UNL\UCBCN\Manager\EditEvent;
use UNL\UCBCN\User as User;
use UNL\UCBCN as BaseUCBCN;
use UNL\UCBCN\Manager\DeleteEvent;
use UNL\UCBCN\calendar\Audience;

class APIEvent extends APICalendar implements ModelInterface, ModelAuthInterface
{
    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        parent::__construct($options);
    }

    // We only need auth if the method is not get
    public function needsAuth (string $method): bool
    {
        if ($method === 'GET') {
            return false;
        }
        return true;
    }

    // We only want to use the API token auth
    public function canUseTokenAuth(string $method): bool
    {
        return true;
    }
    public function canUseCookieAuth(string $method): bool
    {
        return false;
    }

    // Basic CRUD options
    public function run(string $method, array $data, $user): array
    {
        // Multiple ways to query events
        if ($method === 'GET') {
            if (key_exists('recurrence_id', $this->options)) {
                return $this->handleRecurrenceGet();
            }
            if (key_exists('event_datetime_id', $this->options)) {
                return $this->handleDatetimeGet();
            }
            if (key_exists('event_id', $this->options)) {
                return $this->handleEventGet();
            }
        }

        // Other crud options
        if ($method === 'POST') {
            return $this->handlePost($data, $user);
        }
        if ($method === 'PUT') {
            return $this->handlePut($data, $user);
        }
        if ($method === 'DELETE') {
            return $this->handleDelete($user);
        }

        throw new InvalidMethodException('Events Route Invalid Method.');
    }

    // This is for an event id being passed in
    private function handleEventGet(): array
    {
        $this->validateEvent($this->options['event_id']);

        return $this->translateOutgoingEventJSON($this->options['event_id']);
    }

    // This is for an event datetime being passed in
    private function handleDatetimeGet(): array
    {
        $event_occurrence = Occurrence::getById($this->options['event_datetime_id']);

        $this->validateEvent($event_occurrence->event_id);

        return $this->translateOutgoingEventJSON($event_occurrence->event_id);
    }

    // This is for an event datetime recurrence id being passed in
    private function handleRecurrenceGet(): array
    {
        $recurring_date = RecurringDate::getById($this->options['recurrence_id']);

        $this->validateEvent($recurring_date->event_id);

        return $this->translateOutgoingEventJSON($recurring_date->event_id);
    }

    // This is for creating an event
    private function handlePost(array $data, User $user): array
    {
        // Clean up the data
        $this->translateIncomingJSON($data);

        // Make a CreateEvent from the manager
        $createEvent = new CreateEvent(array(
            'calendar_shortname' => $this->calendar->shortname,
            'user' => $user,
            'event_source' => CalendarEvent::SOURCE_CREATE_EVENT_API_V2,
        ));

        // Try to create the event and catch any validation errors
        try {
            $createEvent->handlePost(array(), $data, array());
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        // Output the newly created event
        return $this->translateOutgoingEventJSON($createEvent->event->id);
    }

    // Handles updating the event, this uses only the event id
    private function handlePut(array $data, User $user): array
    {
        // Clean up the data
        $this->translateIncomingJSON($data);

        // Validates the event
        $this->validateEvent($this->options['event_id']);

        // This needs to be unset since the update form is a checkbox instead of radio button
        unset($data['send_to_main']);

        // Try creating an EditEvent object from manager
        try {
            $editEvent = new EditEvent(array(
                'calendar_shortname' => $this->calendar->shortname,
                'user' => $user,
                'event_id' => $this->options['event_id'],
            ));

        // Catch if we do not have access to edit that event
        } catch (\Exception $e) {
            throw new ForbiddenException($e->getMessage());
        }

        // Try to update the event and catch if there are any validation errors
        try {
            $editEvent->handlePost(array(), $data, array());
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        // Output the newly updated event
        return $this->translateOutgoingEventJSON($editEvent->event->id);
    }

    // Handles event deletions, this uses only event id
    private function handleDelete(User $user): array
    {
        // Validates event exists
        $this->validateEvent($this->options['event_id']);

        // Tries to use the deleteEvent, catch errors related permissions and deleting events
        try {
            $deleteEvent = new DeleteEvent(array(
                'calendar_shortname' => $this->calendar->shortname,
                'user' => $user,
                'event_id' => $this->options['event_id'],
            ));
        } catch (\Exception $e) {
            throw new ForbiddenException($e->getMessage());
        }

        // We need for the status
        $calendarHasEvents = CalendarEvent::getByIds($this->calendar->id, $this->options['event_id']);
        if ($calendarHasEvents === false) {
            throw new ValidationException('Calendar does not have that event.');
        }

        // Build the data for the DeleteEvent class
        $data = array(
            'status' => $calendarHasEvents->status === CalendarEvent::STATUS_POSTED ?
                'upcoming' : $calendarHasEvents->status,
        );

        // tries to delete the event, catches validation errors
        try {
            $deleteEvent->handlePost(array(), $data, array());
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        // Returns success message
        return array('Event has been deleted from calendar.');
    }

    // Validates the event id and checks if the calendar has that event in it
    private function validateEvent($event_id)
    {
        if (!isset($event_id) || !is_numeric($event_id)) {
            throw new ValidationException('Missing event id.');
        }
        if ($this->calendar->hasEventById($event_id) === false) {
            throw new ValidationException('That calendar does not have that event with that id.');
        }

        $event = Event::getById($event_id);
        if ($event === false) {
            throw new ValidationException('Invalid ID.');
        }
    }

    // Translates the incoming API json to match the event forms
    private function translateIncomingJSON(array &$event_data)
    {
        $this->replaceJSONKey($event_data, 'webpage', 'webpageurl');
        $this->replaceJSONKey($event_data, 'eventtype', 'type');

        $this->replaceJSONKey($event_data, 'start-date', 'start_date');
        $this->replaceJSONKey($event_data, 'start-time-hour', 'start_time_hour');
        $this->replaceJSONKey($event_data, 'start-time-minute', 'start_time_minute');
        $this->replaceJSONKey($event_data, 'start-time-am-pm', 'start_time_am_pm');

        $this->replaceJSONKey($event_data, 'end-date', 'end_date');
        $this->replaceJSONKey($event_data, 'end-time-hour', 'end_time_hour');
        $this->replaceJSONKey($event_data, 'end-time-minute', 'end_time_minute');
        $this->replaceJSONKey($event_data, 'end-time-am-pm', 'end_time_am_pm');

        $this->replaceJSONKey($event_data, 'recurring-type', 'recurring_type');
        $this->replaceJSONKey($event_data, 'recurs-until-date', 'recurs_until_date');
        $this->replaceJSONKey($event_data, 'event-room', 'room');
        $this->replaceJSONKey($event_data, 'event-directions', 'directions');
        $this->replaceJSONKey($event_data, 'event-additional-public-info', 'additional_public_info');

        $this->replaceJSONKey($event_data, 'contact-website', 'contact_website');
        $this->replaceJSONKey($event_data, 'event-location-additional-public-info', 'l_additional_public_info');
        $this->replaceJSONKey($event_data, 'virtual-location', 'v_location');
        $this->replaceJSONKey($event_data, 'event-virtual-location-additional-public-info', 'v_additional_public_info');

        $this->replaceJSONKey($event_data, 'private-public', 'private_public');

        $this->replaceJSONKey($event_data, 'listing-contact-type', 'contact_type');
        $this->replaceJSONKey($event_data, 'listing-contact-name', 'contact_name');
        $this->replaceJSONKey($event_data, 'listing-contact-phone', 'contact_phone');
        $this->replaceJSONKey($event_data, 'listing-contact-email', 'contact_email');
        $this->replaceJSONKey($event_data, 'listing-contact-website', 'contact_website');

        if (isset($event_data['location'])) {
            if ($event_data['location'] === 'new' || !is_numeric($event_data['location'])) {
                throw new ValidationException('Only existing locations are allowed.');
            }
            $event_data['physical_location_check'] = '1';
        }

        if (isset($event_data['v_location'])) {
            if ($event_data['v_location'] === 'new' || !is_numeric($event_data['v_location'])) {
                throw new ValidationException('Only existing virtual locations are allowed.');
            }
            $event_data['virtual_location_check'] = '1';
        }

        if (isset($event_data['recurring_type'])) {
            $event_data['recurring'] = 'on';
        }

        if (isset($event_data['timezone'])) {
            $timezones = BaseUCBCN::getTimezoneOptions();

            $event_data['timezone'] = ucfirst(strtolower($event_data['timezone']));

            if (!array_key_exists($event_data['timezone'], $timezones)) {
                throw new ValidationException('Invalid timezone.');
            }

            $event_data['timezone'] = $timezones[$event_data['timezone']];
        }

        foreach ($event_data as $key => $value) {
            if (substr($key, 0, 9) === "audience-" && $value === true) {
                $audience_id = substr($key, 9);
                if (!empty($audience_id) && is_numeric($audience_id)) {
                    $validateAudience = Audience::getById($audience_id);

                    if ($validateAudience === false) {
                        throw new ValidationException('Invalid Audience.');
                    }

                    $event_data['target-audience-' . $audience_id] = $audience_id;
                }
                
            }
        }

        // We do not allow images
        unset($event_data['cropped_image_data']);
        unset($event_data['imagedata']);
        unset($event_data['remove_image']);

        // We do not allow people to send events to main with this
        $event_data['send_to_main'] = 'off';
    }

    // Uses the event id and will output nicely formatted json
    public static function translateOutgoingEventJSON(string $event_id): array
    {
        // Validates the event
        $event = Event::getById($event_id);
        if ($event === false) {
            throw new ValidationException('Invalid ID.');
        }

        $event_json = array();

        $event_json['id'] = $event->id;
        $event_json['title'] = $event->title;
        $event_json['subtitle'] = $event->subtitle;
        $event_json['description'] = $event->description;
        $event_json['webpage'] = $event->webpageurl;
        $event_json['private-public'] = $event->approvedforcirculation === '1' ? 'public' : 'private';
        $event_json['listing-contact-name'] = $event->listingcontactname;
        $event_json['listing-contact-phone'] = $event->listingcontactphone;
        $event_json['listing-contact-email'] = $event->listingcontactemail;
        $event_json['listing-contact-url'] = $event->listingcontacturl;
        $event_json['listing-contact-type'] = $event->listingcontacttype;
        $event_json['canceled'] = $event->canceled === '1';

        // Gets the calendar data
        $original_calendar = $event->getOriginCalendar();
        if (isset($original_calendar)) {
            $event_json['original-calendar'] = APICalendar::calendarToJSON($original_calendar->id);
        } else {
            $event_json['original-calendar'] = null;
        }

        // Gets event type data
        $event_json['eventtypes'] = array();
        foreach ($event->getEventTypes() as $event_type_connection) {
            $event_type = $event_type_connection->getType();
            $event_json['eventtypes'][] = array(
                'id' => $event_type->id,
                'name' => $event_type->name,
            );
        }

        // Gets audiences data
        $event_json['audiences'] = array();
        foreach ($event->getAudiences() as $audience_connection) {
            $audience = $audience_connection->getAudience();
            $event_json['audiences'][] = array(
                'id' => $audience->id,
                'name' => $audience->name,
            );
        }

        // Gets image data
        if (isset($event->imagedata)) {
            $event_json['image-url'] = \UNL\UCBCN\Frontend\Controller::$url . '?image&id=' . $event->id;
        } else {
            $event_json['image-url'] = null;
        }

        // Gets occurrences and then gets their data
        $event_json['occurrences'] = array();
        $occurrences = new Occurrences(array(
            'event_id' => $event->id,
        ));
        foreach ($occurrences as $occurrence) {
            $event_json['occurrences'][] = APIEvent::translateOutgoingOccurrenceJSON($occurrence);
        }

        // returns the data
        return $event_json;
    }

    // Takes that occurrence and outputs json
    public static function translateOutgoingOccurrenceJSON(Occurrence $occurrence): array
    {
        // Gets timezone converter
        $timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($occurrence->timezone);

        $occurrence_json = array();

        $occurrence_json['id'] = $occurrence->id;
        $occurrence_json['start-time'] = $timezoneDateTime->format($occurrence->starttime,'c');
        $occurrence_json['end-time'] = $timezoneDateTime->format($occurrence->endtime,'c');
        $occurrence_json['is-all-day'] = APIEvent::isAllDay($occurrence);
        $occurrence_json['event-timezone'] = APICalendar::translateTimezone($occurrence->timezone);
        $occurrence_json['is-recurring'] = $occurrence->isRecurring();
        $occurrence_json['canceled'] = $occurrence->canceled === '1';
        $occurrence_json['event-room'] = $occurrence->room;
        $occurrence_json['event-directions'] = $occurrence->directions;
        $occurrence_json['event-additional-public-info'] = $occurrence->additionalpublicinfo;
        $occurrence_json['event-location-additional-public-info'] = $occurrence->location_additionalpublicinfo;
        $occurrence_json['event-virtual-location-additional-public-info'] = $occurrence->webcast_additionalpublicinfo;

        // Gets the location data
        if (isset($occurrence->location_id) && !empty($occurrence->location_id)) {
            try {
                $occurrence_json['location'] = APILocation::translateOutgoingJSON($occurrence->location_id);
            } catch (ValidationException $e) {
                $occurrence_json['location'] = null;
            }
        } else {
            $occurrence_json['location'] = null;
        }

        // Get the webcast data
        if (isset($occurrence->webcast_id) && !empty($occurrence->webcast_id)) {
            try {
                $occurrence_json['virtual-location'] = APIWebcast::translateOutgoingJSON($occurrence->webcast_id);
            } catch (ValidationException $e) {
                $occurrence_json['virtual-location'] = null;
            }
        } else {
            $occurrence_json['virtual-location'] = null;
        }

        // Gets the recurring dates and their data
        $occurrence_json['recurring-dates'] = array();
        if ($occurrence->isRecurring()) {
            $recurring_dates = $occurrence->getRecurrences();
            foreach ($recurring_dates as $recurring_date) {
                $occurrence_json['recurring-dates'][] = APIEvent::translateOutgoingRecurringDateJSON($recurring_date);
            }
        }

        // Returns the occurrence data
        return $occurrence_json;
    }

    // Takes in the recurring date and outputs json
    public static function translateOutgoingRecurringDateJSON(RecurringDate $recurring_date): array
    {
        $recurring_date_json = array();

        $recurring_date_json['id'] = $recurring_date->id;
        $recurring_date_json['recurring-date'] = $recurring_date->recurringdate;
        $recurring_date_json['recurrence-id'] = $recurring_date->recurrence_id;
        $recurring_date_json['ongoing'] = $recurring_date->ongoing === '1';
        $recurring_date_json['canceled'] = $recurring_date->canceled === '1';

        return $recurring_date_json;
    }


    // Returns a bool on whether the datetime is all day
    private static function isAllDay($occurrence)
    {
        // It must start at midnight to be an all day event
        if (strpos($occurrence->starttime, '00:00:00') === false) {
            return false;
        }

        // It must end at midnight, or not have an end date.
        if (!empty($occurrence->endtime) &&
            strpos($occurrence->endtime, '00:00:00') === false) {
            return false;
        }

        return true;
    }

    // Replaces array key
    private function replaceJSONKey(array &$json_data, string $oldKey, string $newKey): void
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
