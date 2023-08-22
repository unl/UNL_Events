<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Calendars;

class APICalendars implements ModelInterface
{
    public $search_query;

    public $url_match_search = false;
    public function __construct($options = array())
    {
        $this->search_query = $options['q'] ?? "";

        $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->url_match_search = $this->endsWith($url_path, '/search') || $this->endsWith($url_path, '/search/');
    }

    // We only handle get requests
    public function run(string $method, array $data, $user): array
    {
        // When the url matches a search and the method is get
        if ($this->url_match_search && !empty($this->search_query)) {
            if ($method === 'GET') {
                return $this->handleSearchGet();
            }
            throw new InvalidMethodException('Search Calendars only allows get.');
        }

        throw new ValidationException('Calendar Search needs a search query');
    }

    // This will preform a search similar to the normal site search
    private function handleSearchGet()
    {
        $output_array = array();

        $calendars = new Calendars(array(
            'search_query' => $this->search_query,
        ));

        // We only want each event once
        $calendar_ids = array();
        foreach ($calendars as $calendar) {
            if (!isset($calendar_ids[$calendar->id])) {
                $calendar_ids[$calendar->id] = true;
                $output_array[] = APICalendar::calendarToJSON($calendar->id);
            }
        }

        return $output_array;
    }

    // Function for checking if a string ends with a value
    public function endsWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }
}
