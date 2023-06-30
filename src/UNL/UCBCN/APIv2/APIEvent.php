<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Event;
use UNL\UCBCN\Event\Occurrence as Occurrence;
use UNL\UCBCN\Event\Occurrences as Occurrences;
use UNL\UCBCN\Event\RecurringDate;

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
        throw new InvalidMethodException('Events only allows get.');
    }

    private function handleEventGet() {
        if ($this->calendar->hasEventById($this->options['event_id']) === false) {
            throw new ValidationException('That calendar does not have that event with that id.');
        }

        return $this->translateOutgoingEventJSON($this->options['event_id']);
    }
    private function handleDatetimeGet() {
        $event_occurrence = Occurrence::getById($this->options['event_datetime_id']);

        if ($this->calendar->hasEventById($event_occurrence->event_id) === false) {
            throw new ValidationException('That calendar does not have that event with that id.');
        }

        return $this->translateOutgoingEventJSON($event_occurrence->event_id);
    }
    private function handleRecurrenceGet() {
        $recurring_date = RecurringDate::getById($this->options['recurrence_id']);

        if ($this->calendar->hasEventById($recurring_date->event_id) === false) {
            throw new ValidationException('That calendar does not have that event with that id.');
        }

        return $this->translateOutgoingEventJSON($recurring_date->event_id);
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
        $occurrence_json['event-timezone'] = $occurrence->timezone;
        // $occurrence_json['calendar-timezone'] = $this->calendar->defaulttimezone;
        $occurrence_json['is-recurring'] = $occurrence->isRecurring();
        $occurrence_json['canceled'] = $occurrence->canceled === '1';
        $occurrence_json['event-room'] = $occurrence->room;
        $occurrence_json['event-directions'] = $occurrence->directions;
        $occurrence_json['event-additionalpublicinfo'] = $occurrence->additionalpublicinfo;
        $occurrence_json['event-location-additionalpublicinfo'] = $occurrence->location_additionalpublicinfo;
        $occurrence_json['event-virtual-location-additionalpublicinfo'] = $occurrence->webcast_additionalpublicinfo;

        if (isset($occurrence->location_id) && !empty($occurrence->location_id)) {
            $occurrence_json['location'] = APILocation::translateOutgoingJSON($occurrence->location_id);
        } else {
            $occurrence_json['location'] = null;
        }

        if (isset($occurrence->webcast_id) && !empty($occurrence->webcast_id)) {
            $occurrence_json['virtual-location'] = APIWebcast::translateOutgoingJSON($occurrence->webcast_id);
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
}
