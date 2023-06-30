<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Frontend\EventInstance;
use UNL\UCBCN\Frontend\Search;
use UNL\UCBCN\Frontend\Upcoming;

class APIEvents extends APICalendar implements ModelInterface, ModelAuthInterface
{
    public $event_type_filter;
    public $audience_filter;
    public $search_query;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $this->search_query = $options['query'] ?? "";

        $this->event_type_filter = $options['type'] ?? "";
        $this->audience_filter = $options['audience'] ?? "";

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
        $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $upcoming = $this->endsWith($url_path, '/upcoming') || $this->endsWith($url_path, '/upcoming/');
        $search = $this->endsWith($url_path, '/search') || $this->endsWith($url_path, '/search/');

        if ($upcoming) {
            if ($method === 'GET') {
                return $this->handleUpcomingGet();
            }
            throw new InvalidMethodException('Upcoming Events only allows get.');
        }

        if ($search) {
            if ($method === 'GET') {
                return $this->handleSearchGet();
            }
            throw new InvalidMethodException('Search Events only allows get.');
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
}
