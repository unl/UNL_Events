<?php
namespace UNL\UCBCN\APIv2;

use Exception;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Location;
use UNL\UCBCN\Manager\WebcastUtility as WebcastUtility;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Webcast as Webcast;
use UNL\UCBCN\User as User;

class APIWebcast implements ModelInterface, ModelAuthInterface
{
    public $options = array();

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
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

    // Basic CRUD but we do not allow delete
    public function run(string $method, array $data, $user): array
    {
        if ($method === 'GET') {
            return $this->handleGet();
        }
        if ($method === 'POST') {
            return $this->handlePost($data, $user);
        }
        if ($method === 'PUT') {
            return $this->handlePut($data, $user);
        }

        throw new InvalidMethodException('Virtual Location route invalid method.');
    }

    // Looks up webcast based on id
    private function handleGet()
    {
        if (!isset($this->options['webcast_id']) || !is_numeric($this->options['webcast_id'])) {
            throw new ValidationException('Invalid virtual location ID.');
        }

        return $this->translateOutgoingJSON($this->options['webcast_id']);
    }

    // Creates new webcast
    private function handlePost(array $data, User $user): array
    {
        // Cleans up incoming data
        $this->translateIncomingJSON($data);

        // Makes sure we are creating a new virtual location
        if (!isset($data['v_location']) || $data['v_location'] !== 'new') {
            $data['v_location'] = 'new';
        }

        // Validates the webcast values and get the calendar set to its corresponding calendar
        $calendar = null;
        $this->validateIncomingWebcast($data, $user, $calendar);

        // Uses webcast utility to create new webcast
        $new_webcast = WebcastUtility::addWebcast($data, $user, $calendar);
        if ($new_webcast === false) {
            throw new ServerErrorException('Failed to create virtual location.');
        }

        // Return newly created webcast
        return $this->translateOutgoingJSON($new_webcast->id);
    }

    // Update existing virtual location
    private function handlePut(array $data, User $user): array
    {
        $data['v_location'] = $this->options['webcast_id'] ?? 'new';

        // Cleans up incoming data
        $this->translateIncomingJSON($data);

        // Makes sure we have a virtual location id and it is valid
        if (!isset($data['v_location']) || !is_numeric($data['v_location'])) {
            throw new ValidationException('Invalid Location ID.');
        }
        $webcast = Webcast::getByID($data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Location ID');
        }

        // Make sure we have access to that virtual location
        if (
            !(isset($webcast->user_id) && $webcast->user_id === $user->uid) &&
            !(isset($webcast->calendar_id) && $this->userHasAccessToCalendar($user, $webcast->calendar_id))
        ) {
            throw new ForbiddenException('You do not have access to modify that virtual location.');
        }

        // Validates the webcast values and get the calendar set to its corresponding calendar
        $calendar = null;
        $this->validateIncomingWebcast($data, $user, $calendar);

        // Uses webcast utility to update the virtual location
        $new_webcast = WebcastUtility::updateWebcast($data, $user, $calendar);
        if ($new_webcast === false) {
            throw new ServerErrorException('Failed to create virtual location.');
        }

         // Return newly updated webcast
        return $this->translateOutgoingJSON($new_webcast->id);
    }

    // Validates the incoming values from the API
    private function validateIncomingWebcast($data, $user, &$calendar)
    {
        // If the calendar is set we will make sure its valid
        if (isset($data['v_location_save_calendar']) && $data['v_location_save_calendar'] === 'on') {
            if (!isset($data['calendar-id'])) {
                throw new ValidationException('Missing Calendar ID.');
            }

            if (!is_numeric($data['calendar-id'])) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            $calendar = Calendar::getByID($data['calendar-id']);
            if ($calendar === false) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            // We will check that the user have access to that calendar
            if (!$this->userHasAccessToCalendar($user, $calendar->id)) {
                throw new ForbiddenException('You do not have access to that calendar.');
            }
        }

        // We will use the webcast utility to validate the other inputs
        $validation_data = WebcastUtility::validateWebcast($data);
        if (!$validation_data['valid']) {
            throw new ValidationException($validation_data['message']);
        }
    }

    // Function for checking if the user has access to add and edit virtual locations on a calendar
    public function userHasAccessToCalendar(User $user, string $calendar_id)
    {
        $edit_permission = Permission::getByName('Event Edit');
        $create_permission = Permission::getByName('Event Create');

        return $user->hasPermission($edit_permission->id, $calendar_id) &&
            $user->hasPermission($create_permission->id, $calendar_id);
    }

    // Converts the incoming JSON from API to match what its like in the web form
    private function translateIncomingJSON(array &$webcast_data)
    {
        $new_v_location = array();

        if (isset($webcast_data['save-user'])
            && ($webcast_data['save-user'] === 'true' || $webcast_data['save-user'] === true)
        ) {
            $webcast_data['v_location_save'] = 'on';
        }

        if (isset($webcast_data['save-calendar'])
            && ($webcast_data['save-calendar'] === 'true' || $webcast_data['save-calendar'] === true)
        ) {
            $webcast_data['v_location_save_calendar'] = 'on';
        }

        $new_v_location['title'] = $webcast_data['name'] ?? "";
        $new_v_location['url'] = $webcast_data['url'] ?? "";
        $new_v_location['additionalinfo'] = $webcast_data['default-additional-public-info'] ?? "";
        $new_v_location['user_id'] = $webcast_data['user-id'] ?? "";
        $new_v_location['calendar_id'] = $webcast_data['calendar-id'] ?? "";

        $webcast_data['new_v_location'] = $new_v_location;
    }

    // Takes a webcast ID and converts it to nicely formatted JSON
    public static function translateOutgoingJSON(string $webcast_id): array
    {
        $webcast = Webcast::getByID($webcast_id);

        if ($webcast === false) {
            throw new ValidationException('Invalid virtual location ID.');
        }

        $webcast_json = $webcast->toJSON();

        APIWebcast::replaceJSONKey($webcast_json, 'v-location',          'id'  );
        APIWebcast::replaceJSONKey($webcast_json, 'new-v-location-name', 'name');
        APIWebcast::replaceJSONKey($webcast_json, 'new-v-location-url',  'url' );
        APIWebcast::replaceJSONKey(
            $webcast_json,
            'new-v-location-additional-public-info',
            'default-additional-public-info'
        );

        return $webcast_json;
    }

    // Replace keys in array
    private static function replaceJSONKey(&$json_data, $oldKey, $newKey)
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
