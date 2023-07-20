<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Event\Occurrences;
use UNL\UCBCN\Events;
use UNL\UCBCN\Location;
use UNL\UCBCN\Webcast;
use UNL\UCBCN\Frontend\EventInstance;
use UNL\UCBCN\Frontend\Search;
use UNL\UCBCN\Frontend\Upcoming;

class APIEvents extends APICalendar implements ModelInterface, ModelAuthInterface
{
    public $event_type_filter;
    public $audience_filter;
    public $search_query;

    public $url_match_search = false;
    public $url_match_pending = false;

    public $limit = 100;
    public $offset = 0;
    public $max_limit = 500;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $this->search_query = $options['query'] ?? "";

        $this->event_type_filter = $options['type'] ?? "";
        $this->audience_filter = $options['audience'] ?? "";

        if (!isset($options['limit']) ||
            empty($options['limit']) ||
            intval($options['limit']) > $this->max_limit ||
            intval($options['limit']) <= 0
        ) {
            $options['limit'] = $this->max_limit;
        }

        if (!isset($options['offset']) || empty($options['offset']) || intval($options['offset']) <= 0) {
            $options['offset'] = 0;
        }

        $this->limit = $options['limit'] ?? $this->limit;
        $this->offset = $options['offset'] ?? $this->offset;

        $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->url_match_search   = $this->endsWith($url_path, '/search') || $this->endsWith($url_path, '/search/');
        $this->url_match_pending  = $this->endsWith($url_path, '/pending') || $this->endsWith($url_path, '/pending/');

        parent::__construct($options);
    }

    // We only need auth for getting a list of the pending events
    public function needsAuth (string $method): bool
    {
        if ($this->url_match_pending && $method === 'GET') {
            return true;
        }

        if ($method === 'GET') {
            return false;
        }
        return true;
    }

    // We only want to use the token auth
    public function canUseTokenAuth(string $method): bool
    {
        return true;
    }
    public function canUseCookieAuth(string $method): bool
    {
        return false;
    }

    // We only handle get requests
    public function run(string $method, array $data, $user): array
    {
        // When the url matches a search and the method is get
        if ($this->url_match_search) {
            if ($method === 'GET') {
                return $this->handleSearchGet();
            }
            throw new InvalidMethodException('Search Events only allows get.');
        }

        // When the url matches a pending and the method is get
        if ($this->url_match_pending) {
            if ($method === 'GET') {
                return $this->handlePendingGet($user);
            }
            throw new InvalidMethodException('Pending Events only allows get.');
        }

        // When the url has a location id and the method is get
        if ($method === 'GET'
            && key_exists('location_id', $this->options)
            && is_numeric($this->options['location_id'])
        ) {
            return $this->handleLocationGet();
        }

        // When the url has a webcast id and the method is get
        if ($method === 'GET'
            && key_exists('webcast_id', $this->options)
            && is_numeric($this->options['webcast_id'])
        ) {
            return $this->handleWebcastGet();
        }

        // Handles the regular gets
        if ($method === 'GET') {
            return $this->handleGet();
        }

        throw new InvalidMethodException('Events only allows get.');
    }

    // This is just getting a list of the calendar's events
    private function handleGet(): array
    {
        $output_array = array();

        $calendar_events = $this->calendar->getEvents(Calendar::STATUS_POSTED, $this->limit, $this->offset);
        foreach ($calendar_events as $event) {
            $output_array[] = APIEvent::translateOutgoingEventJSON($event->id);
        }

        return $output_array;
    }

    // This will preform a search similar to the normal site search
    private function handleSearchGet()
    {
        $output_array = array();

        // Creates a new search object with all the things
        $search_events = new Search(array(
            'calendar' => $this->calendar,
            'q' => $this->search_query,
            'type' => $this->event_type_filter,
            'audience' => $this->audience_filter,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'format' => 'json',
        ));

        // We only want each event once
        $event_ids = array();
        foreach ($search_events as $event_occurrence) {
            if (!isset($event_ids[$event_occurrence->event->id])) {
                $event_ids[$event_occurrence->event->id] = true;
                $output_array[] = APIEvent::translateOutgoingEventJSON($event_occurrence->event->id);
            }
        }

        return $output_array;
    }

    // This will get a list of the pending events
    private function handlePendingGet($user)
    {
        $output_array = array();

        // Check if the user has access to that calendar
        if (!$this->calendar->hasUser($user)) {
            throw new ForbiddenException('You do not have access to delete this calendar.');
        }

        // Get the pending events
        $pending_events = $this->calendar->getEvents(Calendar::STATUS_PENDING, $this->limit, $this->offset);
        foreach ($pending_events as $event) {
            $output_array[] = APIEvent::translateOutgoingEventJSON($event->id);
        }

        return $output_array;
    }

    // This will find the events on a calendar that are at a particular location
    private function handleLocationGet()
    {
        $output_array = array();

        // Get the location
        $location = Location::getById($this->options['location_id']);
        if ($location === false) {
            throw new ValidationException('Invalid location id');
        }

        // find all occurrences by location
        $location_occurrences = new Occurrences(array(
            'location_id' => $location->id,
            'calendar_id' => $this->calendar->id,
        ));

        // We only want each event once
        $event_ids = array();
        foreach ($location_occurrences as $event_occurrence) {
            if (!isset($event_ids[$event_occurrence->event_id])) {
                $event_ids[$event_occurrence->event_id] = true;
                $output_array[] = APIEvent::translateOutgoingEventJSON($event_occurrence->event_id);
            }
        }

        return $output_array;
    }

    // This will find the events on a calendar that are at a particular virtual location
    private function handleWebcastGet()
    {
        $output_array = array();

        // Get the virtual location
        $webcast = Webcast::getById($this->options['webcast_id']);
        if ($webcast === false) {
            throw new ValidationException('Invalid virtual location id');
        }

        // find all occurrences at that virtual location
        $webcast_occurrences = new Occurrences(array(
            'webcast_id' => $webcast->id,
            'calendar_id' => $this->calendar->id,
        ));

        // We only want each event once
        $event_ids = array();
        foreach ($webcast_occurrences as $event_occurrence) {
            if (!isset($event_ids[$event_occurrence->event_id])) {
                $event_ids[$event_occurrence->event_id] = true;
                $output_array[] = APIEvent::translateOutgoingEventJSON($event_occurrence->event_id);
            }
        }

        return $output_array;
    }
}
