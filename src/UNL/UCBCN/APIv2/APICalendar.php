<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Calendar as Calendar;
use UNL\UCBCN\Manager\CreateCalendar as CreateCalendar;
use UNL\UCBCN\User;
use UNL\UCBCN as BaseUCBCN;
use UNL\UCBCN\Permission;

class APICalendar implements ModelInterface, ModelAuthInterface
{
    public $options = array();
    public $calendar = false;
    private $calendar_create = false;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->calendar_create = $this->endsWith($url_path, '/calendar') || $this->endsWith($url_path, '/calendar/');

        if ($this->calendar_create) {
            return;
        }

        if (!isset($this->options['calendar_id'])) {
            throw new ValidationException('Missing Calendar Id');
        }

        if (is_numeric($this->options['calendar_id'])) {
            $this->calendar = Calendar::getById($this->options['calendar_id']);
        }

        if ($this->calendar === false) {
            $this->calendar = Calendar::getByShortname($this->options['calendar_id']);
        }

        if ($this->calendar === false) {
            throw new ValidationException('Invalid Calendar Id');
        }
    }

    // We need auth if we are not using get
    public function needsAuth (string $method): bool
    {
        if ($method === 'GET') {
            return false;
        }
        return true;
    }

    // We can only use the API token
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
        throw new NotImplementedException();

        if ($method === 'GET') {
            return $this->calendarToJSON($this->calendar->id);
        }

        if ($method === 'POST') {
            return $this->handlePost($data, $user);
        }

        if ($method === 'PUT') {
            return $this->handlePut($data, $user);
        }

        if ($method === 'DELETE') {
            // Check if we can delete the calendar
            $delete_permission = Permission::getByName('Calendar Delete');
            if (!$user->hasPermission($delete_permission->id, $this->calendar->id)) {
                throw new ForbiddenException('You do not have access to delete this calendar.');
            }

            // If we do have access we will delete the calendar and output the data
            $this->calendar->delete();
            return array($this->calendar->name . ' has been deleted.');
        }

        throw new InvalidMethodException('Calendar route invalid method.');
    }

    // Handle when a new calendar is being created
    private function handlePost(array $data, User $user): array
    {
        // Clean up data
        $this->translateIncomingJSON($data);

        // Make a new CreateCalendar object from manager
        $createCalendar = new CreateCalendar(array(
            'user' => $user,
        ));

        // Try creating calendar and check for validation errors
        try {
            $createCalendar->createCalendar($data);
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        // If successful then return the newly created calendar
        return $this->calendarToJSON($createCalendar->calendar->id);
    }

    // Handle updating calendar data
    private function handlePut(array $data, User $user): array
    {
        // Clean up data
        $this->translateIncomingJSON($data);

        // Try using the CreateCalendar from manager
        try {
            $createCalendar = new CreateCalendar(array(
                'calendar_shortname' => $this->calendar->shortname,
                'user' => $user,
            ));

        // Catch errors with validation or access
        } catch(\Exception $e) {
            if ($e->getCode() === 403) {
                throw new ForbiddenException('You can not edit that calendar.');
            } else {
                throw new ValidationException($e->getMessage());
            }
        }

        // Try updating and catch any validation errors
        try {
            $createCalendar->updateCalendar($data);
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        // Return the newly updated calendar
        return $this->calendarToJSON($createCalendar->calendar->id);
    }

    // Convert calendar id to json of the calendar
    public static function calendarToJSON(string $calendar_id): array
    {
        // Get calendar and check if it exists
        $calendar = Calendar::getById($calendar_id);
        if ($calendar === false) {
            throw new ValidationException('Invalid Calendar Id');
        }

        // Clean up event release preference
        $event_release_preference = null;
        switch ($calendar->eventreleasepreference) {
            case Calendar::EVENT_RELEASE_PREFERENCE_DEFAULT:
                $event_release_preference = '';
                break;
            case Calendar::EVENT_RELEASE_PREFERENCE_IMMEDIATE:
                $event_release_preference = 'immediate';
                break;
            case Calendar::EVENT_RELEASE_PREFERENCE_PENDING:
                $event_release_preference = 'pending';
                break;
            default:
                $event_release_preference = null;
        }

        // Convert to bool
        $recommendations_within_account = $calendar->recommendationswithinaccount === '1';

        // The rest of the info can be made into an associative array
        return array(
            'id' => $calendar->id,
            'name' => $calendar->name,
            'short-name' => $calendar->shortname,
            'default-timezone' => APICalendar::translateTimezone($calendar->defaulttimezone),
            'webpage' => empty($calendar->website) ? null : $calendar->website,
            'email-list' =>  empty($calendar->emaillists) ? null : $calendar->emaillists,
            'event-release-preference' => $event_release_preference,
            'recommendations-within-account' => $recommendations_within_account,
        );
    }

    // Translate json from API to the data that the manager code will use
    private function translateIncomingJSON(array &$calendar_data)
    {
        $this->replaceJSONKey($calendar_data, 'webpage', 'website');
        $this->replaceJSONKey($calendar_data, 'short-name', 'shortname');
        $this->replaceJSONKey($calendar_data, 'email-lists', 'email_lists');
        $this->replaceJSONKey($calendar_data, 'recommend-within-account', 'recommend_within_account');

        $calendar_data['event_release_preference'] = strtolower($calendar_data['event-release-preference'] ?? "");

        $timezones = BaseUCBCN::getTimezoneOptions();
        $calendar_data['default-timezone'] = ucfirst(strtolower($calendar_data['default-timezone'] ?? ""));

        if (!array_key_exists($calendar_data['default-timezone'], $timezones)) {
            throw new ValidationException('Invalid timezone');
        }

        $calendar_data['defaulttimezone'] = $timezones[$calendar_data['default-timezone'] ?? ""];
    }

    // Convert php time zone to short name from UCBCN
    public static function translateTimezone($phpTimeZone)
    {
        $timezones = BaseUCBCN::getTimezoneOptions();

        return array_search($phpTimeZone, $timezones);
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

    // Replace keys in array
    private function replaceJSONKey(array &$json_data, string $oldKey, string $newKey): void
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
