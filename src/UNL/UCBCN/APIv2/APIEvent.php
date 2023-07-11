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

class APIEvent extends APICalendar implements ModelInterface, ModelAuthInterface
{
    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        parent::__construct($options);
    }

    public function needsAuth(string $method): bool
    {
        if ($method === 'GET') {
            return false;
        }
        return true;
    }
    public function canUseTokenAuth(string $method): bool
    {
        return true;
    }
    public function canUseCookieAuth(string $method): bool
    {
        return false;
    }

    public function run(string $method, array $data, $user): array
    {
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

        if ($method === 'POST') {
            return $this->handlePost($data, $user);
        }
        if ($method === 'PUT') {
            return $this->handlePut($data, $user);
        }
        if ($method === 'DELETE') {
            return $this->handleDelete($user);
        }

        throw new InvalidMethodException('Events only allows get.');
    }

    private function handleEventGet(): array
    {
        $this->validateEvent($this->options['event_id']);

        return $this->translateOutgoingEventJSON($this->options['event_id']);
    }
    private function handleDatetimeGet(): array
    {
        $event_occurrence = Occurrence::getById($this->options['event_datetime_id']);

        $this->validateEvent($event_occurrence->event_id);

        return $this->translateOutgoingEventJSON($event_occurrence->event_id);
    }
    private function handleRecurrenceGet(): array
    {
        $recurring_date = RecurringDate::getById($this->options['recurrence_id']);

        $this->validateEvent($recurring_date->event_id);

        return $this->translateOutgoingEventJSON($recurring_date->event_id);
    }

    private function handlePost(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        $createEvent = new CreateEvent(array(
            'calendar_shortname' => $this->calendar->shortname,
            'user' => $user,
            'event_source' => CalendarEvent::SOURCE_CREATE_EVENT_API_V2,
        ));

        try {
            $createEvent->handlePost(array(), $data, array());
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->translateOutgoingEventJSON($createEvent->event->id);
    }

    private function handlePut(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        $this->validateEvent($this->options['event_id']);

        // This needs to be unset since the update form is a checkbox instead of radio button
        unset($data['send_to_main']);

        try {
            $editEvent = new EditEvent(array(
                'calendar_shortname' => $this->calendar->shortname,
                'user' => $user,
                'event_id' => $this->options['event_id'],
            ));
        } catch (\Exception $e) {
            throw new ForbiddenException($e->getMessage());
        }
        

        try {
            $editEvent->handlePost(array(), $data, array());
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->translateOutgoingEventJSON($editEvent->event->id);
    }

    private function handleDelete(User $user):array
    {
        $this->validateEvent($this->options['event_id']);

        try {
            $deleteEvent = new DeleteEvent(array(
                'calendar_shortname' => $this->calendar->shortname,
                'user' => $user,
                'event_id' => $this->options['event_id'],
            ));
        } catch (\Exception $e) {
            throw new ForbiddenException($e->getMessage());
        }

        $calendarHasEvents = CalendarEvent::getByIds($this->calendar->id, $this->options['event_id']);
        if ($calendarHasEvents === false) {
            throw new ValidationException('Calendar does not have that event.');
        }

        $data = array(
            'status' => $calendarHasEvents->status === CalendarEvent::STATUS_POSTED ? 'upcoming' : $calendarHasEvents->status,
        );

        try {
            $deleteEvent->handlePost(array(), $data, array());
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        return array('Event has been deleted from calendar.');
    }

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

    private function translateIncomingJSON(array &$event_data)
    {
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
        $this->replaceJSONKey($event_data, 'additional-public-info', 'additional_public_info');

        $this->replaceJSONKey($event_data, 'contact-website', 'contact_website');
        $this->replaceJSONKey($event_data, 'location-additional-public-info', 'l_additional_public_info');
        $this->replaceJSONKey($event_data, 'virtual-location', 'v_location');
        $this->replaceJSONKey($event_data, 'virtual-location-additional-public-info', 'v_additional_public_info');

        $this->replaceJSONKey($event_data, 'private-public', 'private_public');

        $this->replaceJSONKey($event_data, 'contact-type', 'contact_type');
        $this->replaceJSONKey($event_data, 'contact-name', 'contact_name');
        $this->replaceJSONKey($event_data, 'contact-phone', 'contact_phone');
        $this->replaceJSONKey($event_data, 'contact-email', 'contact_email');
        $this->replaceJSONKey($event_data, 'contact-website', 'contact_website');

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

        unset($event_data['cropped_image_data']);
        unset($event_data['imagedata']);
        unset($event_data['remove_image']);

        $event_data['send_to_main'] = 'off';
    }


    public static function translateOutgoingEventJSON(string $event_id):array
    {
        $event = Event::getById($event_id);

        if ($event === false) {
            throw new ValidationException('Invalid ID.');
        }

        $event_json = array();

        $event_json['id'] = $event->id;
        $event_json['title'] = $event->title;
        $event_json['subtitle'] = $event->subtitle;
        $event_json['description'] = $event->description;
        $event_json['webpageurl'] = $event->webpageurl;
        $event_json['approved-for-circulation'] = $event->approvedforcirculation === '1';
        $event_json['listing-contact-uid'] = $event->listingcontactuid;
        $event_json['listing-contact-name'] = $event->listingcontactname;
        $event_json['listing-contact-phone'] = $event->listingcontactphone;
        $event_json['listing-contact-email'] = $event->listingcontactemail;
        $event_json['listing-contact-url'] = $event->listingcontacturl;
        $event_json['listing-contact-type'] = $event->listingcontacttype;
        $event_json['canceled'] = $event->canceled === '1';

        $original_calendar = $event->getOriginCalendar();
        if (isset($original_calendar)) {
            $event_json['original-calendar'] = APICalendar::calendarToJSON($original_calendar->id);
        } else {
            $event_json['original-calendar'] = null;
        }

        $event_json['event-types'] = array();
        foreach($event->getEventTypes() as $event_type_connection) {
            $event_type = $event_type_connection->getType();
            $event_json['event-types'][] = array(
                'id' => $event_type->id,
                'name' => $event_type->name,
            );
        }

        $event_json['audiences'] = array();
        foreach($event->getAudiences() as $audience_connection) {
            $audience = $audience_connection->getAudience();
            $event_json['audiences'][] = array(
                'id' => $audience->id,
                'name' => $audience->name,
            );
        }

        if (isset($event->imagedata)) {
            $event_json['image-url'] = \UNL\UCBCN\Frontend\Controller::$url . '?image&id=' . $event->id;
        } else {
            $event_json['image-url'] = null;
        }

        $event_json['occurrences'] = array();
        $occurrences = new Occurrences(array(
            'event_id' => $event->id,
        ));

        foreach ($occurrences as $occurrence) {
            $event_json['occurrences'][] = APIEvent::translateOutgoingOccurrenceJSON($occurrence);
        }

        return $event_json;
    }

    public static function translateOutgoingOccurrenceJSON(Occurrence $occurrence): array
    {
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
        $occurrence_json['event-additionalpublicinfo'] = $occurrence->additionalpublicinfo;
        $occurrence_json['event-location-additionalpublicinfo'] = $occurrence->location_additionalpublicinfo;
        $occurrence_json['event-virtual-location-additionalpublicinfo'] = $occurrence->webcast_additionalpublicinfo;

        if (isset($occurrence->location_id) && !empty($occurrence->location_id)) {
            try {
                $occurrence_json['location'] = APILocation::translateOutgoingJSON($occurrence->location_id);
            } catch (ValidationException $e) {
                $occurrence_json['location'] = null;
            }
        } else {
            $occurrence_json['location'] = null;
        }

        if (isset($occurrence->webcast_id) && !empty($occurrence->webcast_id)) {
            try {
                $occurrence_json['virtual-location'] = APIWebcast::translateOutgoingJSON($occurrence->webcast_id);
            } catch (ValidationException $e) {
                $occurrence_json['virtual-location'] = null;
            }
        } else {
            $occurrence_json['virtual-location'] = null;
        }

        $occurrence_json['recurring-dates'] = array();
        if ($occurrence->isRecurring()) {
            $recurring_dates = $occurrence->getRecurrences();
            foreach ($recurring_dates as $recurring_date) {
                $occurrence_json['recurring-dates'][] = APIEvent::translateOutgoingRecurringDateJSON($recurring_date);
            }
        }

        return $occurrence_json;
    }

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

    private static function isAllDay($occurrence)
    {
        //It must start at midnight to be an all day event
        if (strpos($occurrence->starttime, '00:00:00') === false) {
            return false;
        }

        //It must end at midnight, or not have an end date.
        if (!empty($occurrence->endtime) &&
            strpos($occurrence->endtime, '00:00:00') === false) {
            return false;
        }

        return true;
    }

    private function replaceJSONKey(array &$json_data, string $oldKey, string $newKey): void
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
