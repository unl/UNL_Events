<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Frontend\EventInstance;
use UNL\UCBCN\Frontend\Search;
use UNL\UCBCN\Frontend\Upcoming;

class APIEvents extends APICalendar implements ModelInterface, ModelAuthInterface
{
    public $event_type_filter;
    public $audience_filter;
    public $search_query;

    public $url_match_upcoming = false;
    public $url_match_search = false;
    public $url_match_pending = false;

    public function __construct($options = array())
    {
        //TODO Get Limits and Offset working on these
        $this->options = $options + $this->options;

        $this->search_query = $options['query'] ?? "";

        $this->event_type_filter = $options['type'] ?? "";
        $this->audience_filter = $options['audience'] ?? "";

        $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->url_match_upcoming = $this->endsWith($url_path, '/upcoming') || $this->endsWith($url_path, '/upcoming/');
        $this->url_match_search  = $this->endsWith($url_path, '/search') || $this->endsWith($url_path, '/search/');
        $this->url_match_pending   = $this->endsWith($url_path, '/pending') || $this->endsWith($url_path, '/pending/');

        parent::__construct($options);
    }

    public function needsAuth(string $method): bool
    {
        if ($this->url_match_pending && $method === 'GET') {
            return true;
        }
        
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
        if ($this->url_match_upcoming) {
            if ($method === 'GET') {
                return $this->handleUpcomingGet();
            }
            throw new InvalidMethodException('Upcoming Events only allows get.');
        }

        if ($this->url_match_search) {
            if ($method === 'GET') {
                return $this->handleSearchGet();
            }
            throw new InvalidMethodException('Search Events only allows get.');
        }

        if ($this->url_match_pending) {
            if ($method === 'GET') {
                return $this->handlePendingGet();
            }
            throw new InvalidMethodException('Pending Events only allows get.');
        }

        throw new NotFoundException();
    }

    private function handleUpcomingGet() {
        $output_array = array();

        $upcoming_events = new Upcoming(array(
            'calendar' => $this->calendar,
            'type' => $this->event_type_filter,
            'audience' => $this->audience_filter,
        ));

        foreach ($upcoming_events as $event_occurrence) {
            $output_array[] = APIEvent::translateOutgoingEventJSON($event_occurrence->event->id);
        }

        return $output_array;
    }

    private function handleSearchGet() {
        $output_array = array();

        $search_events = new Search(array(
            'calendar' => $this->calendar,
            'q' => $this->search_query,
            'type' => $this->event_type_filter,
            'audience' => $this->audience_filter,
        ));

        foreach ($search_events as $event_occurrence) {
            $output_array[] = APIEvent::translateOutgoingEventJSON($event_occurrence->event->id);
        }

        return $output_array;
    }

    private function handlePendingGet() {
        $output_array = array();

        $pending_events = $this->calendar->getEvents(Calendar::STATUS_PENDING);

        foreach ($pending_events as $event) {
            $output_array[] = APIEvent::translateOutgoingEventJSON($event->id);
        }

        return $output_array;
    }
}
