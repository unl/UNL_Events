<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\User as User;
use UNL\UCBCN\Manager\LocationUtility as LocationUtility;
use UNL\UCBCN\Manager\WebcastUtility as WebcastUtility;

class APIMe implements ModelInterface, ModelAuthInterface
{
    public $options = array();
    private $url_match_calendars = false;
    private $url_match_locations = false;
    private $url_match_webcasts = false;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->url_match_calendars = $this->endsWith($url_path, '/calendars')
            || $this->endsWith($url_path, '/calendars/');
        $this->url_match_locations = $this->endsWith($url_path, '/locations')
            || $this->endsWith($url_path, '/locations/');
        $this->url_match_webcasts = $this->endsWith($url_path, '/virtual-locations')
            || $this->endsWith($url_path, '/virtual-locations/');
    }

    // We need auth if we are not using get
    public function needsAuth (string $method): bool
    {
        return true;
    }

    // We can only use the API token
    public function canUseTokenAuth(string $method): bool
    {
        return true;
    }
    public function canUseCookieAuth(string $method): bool
    {
        return true;
    }

    // Basic CRUD options
    public function run(string $method, array $data, $user): array
    {
        if ($this->url_match_calendars) {
            if ($method === 'GET') {
                return $this->getUserCalendars($user);
            }
            throw new InvalidMethodException('User Calendars route only allows get.');
        }

        if ($this->url_match_locations) {
            if ($method === 'GET') {
                return $this->getUserLocations($user);
            }
            throw new InvalidMethodException('User Locations route only allows get.');
        }

        if ($this->url_match_webcasts) {
            if ($method === 'GET') {
                return $this->getUserWebcasts($user);
            }
            throw new InvalidMethodException('User Virtual Locations route only allows get.');
        }

        if ($method !== 'GET') {
            throw new InvalidMethodException('Me route invalid method.');
        }

        return $this->getCurrentUser($user);
    }

    public function getCurrentUser(User $user): array
    {
        return array('uid' => $user->uid);
    }

    public function getUserCalendars(User $user): array
    {
        $calendars = $user->getCalendars();

        $output_data = array();
        foreach ($calendars as $calendar) {
            $output_data[] = APICalendar::calendarToJSON($calendar->id);
        }
        return $output_data;
    }

    public function getUserLocations(User $user): array
    {
        $locations = LocationUtility::getUserLocations($user);

        $output_data = array();
        foreach ($locations as $location) {
            $output_data[] = APILocation::translateOutgoingJSON($location->id);
        }
        return $output_data;
    }

    public function getUserWebcasts(User $user): array
    {
        $webcasts = WebcastUtility::getUserWebcasts($user);

        $output_data = array();
        foreach ($webcasts as $webcast) {
            $output_data[] = APIWebcast::translateOutgoingJSON($webcast->id);
        }
        return $output_data;
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
