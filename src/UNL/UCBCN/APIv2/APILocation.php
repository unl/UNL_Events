<?php
namespace UNL\UCBCN\APIv2;

use Exception;
use UNL\UCBCN\Calendar as Calendar;
use UNL\UCBCN\Location as Location;
use \UNL\UCBCN\Manager\LocationUtility as LocationUtility;
use UNL\UCBCN\User as User;
use UNL\UCBCN\Permission as Permission;

class APILocation implements ModelInterface, ModelAuthInterface
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

    // We can run everything except delete
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

        throw new InvalidMethodException('Location route invalid method.');
    }

    // This is for location look up or if we are wanting a list of the standard locations
    private function handleGet(): array
    {
        if (isset($this->options['location_id']) && is_numeric($this->options['location_id'])) {
            return $this->getLocationById($this->options['location_id']);
        } elseif (isset($this->options['location_id']) && $this->options['location_id'] === 'standard') {
            return $this->getLocationByStandard();
        }

        throw new ValidationException('Invalid location ID.');
    }

    // This is for making a new location
    private function handlePost(array $data, User $user): array
    {
        // Clean up data
        $this->translateIncomingJSON($data);

        // We only want to make new locations
        if (!isset($data['location']) || $data['location'] !== 'new') {
            $data['location'] = 'new';
        }

        // Check if the calendar is ok if it is set
        $calendar = null;
        if (isset($data['location_save_calendar']) && $data['location_save_calendar'] === 'on') {
            if (!isset($data['calendar-id']) || !is_numeric($data['calendar-id'])) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            $calendar = Calendar::getByID($data['calendar-id']);
            if ($calendar === false) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            // Check if you have access to the calendar
            if (!$this->userHasAccessToCalendar($user, $calendar->id)) {
                throw new ForbiddenException('You do not have access to that calendar.');
            }
        }

        // Use the location utility to validate the location data
        $validation_data = LocationUtility::validateLocation($data);
        if (!$validation_data['valid']) {
            throw new ValidationException($validation_data['message']);
        }

        // Use the location utility to make a new location
        $new_location = LocationUtility::addLocation($data, $user, $calendar);
        if ($new_location === false) {
            throw new ServerErrorException('Failed to create location.');
        }

        // return the newly made location
        return $this->translateOutgoingJSON($new_location->id);
    }

    // This is for updating a location
    private function handlePut(array $data, User $user): array
    {
        // Clean up the data
        $this->translateIncomingJSON($data);

        // Checks if the location is valid
        if (!isset($data['location']) || !is_numeric($data['location'])) {
            throw new ValidationException('Invalid Location ID.');
        }
        $location = Location::getByID($data['location']);
        if ($location === null) {
            throw new ValidationException('Invalid Location ID');
        }

        // Checks if the user has this location saved or if it is saved to a calendar they have access to
        if (
            !(isset($location->user_id) && $location->user_id === $user->uid) &&
            !(isset($location->calendar_id) && $this->userHasAccessToCalendar($user, $location->calendar_id))
        ) {
            throw new ForbiddenException('You do not have access to modify that location.');
        }

        // Check if the calendar is ok if it is set
        $calendar = null;
        if (isset($data['location_save_calendar']) && $data['location_save_calendar'] === 'on') {
            if (!isset($data['calendar-id']) || !is_numeric($data['calendar-id'])) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            $calendar = Calendar::getByID($data['calendar-id']);
            if ($calendar === false) {
                throw new ValidationException('Invalid Calendar ID.');
            }

            // Check if you have access to the calendar
            if (!$this->userHasAccessToCalendar($user, $calendar->id)) {
                throw new ForbiddenException('You do not have access to that calendar.');
            }
        }

        // Use the location utility to validate the location data
        $validation_data = LocationUtility::validateLocation($data);
        if (!$validation_data['valid']) {
            throw new ValidationException($validation_data['message']);
        }

        // Use the location utility to update the location
        $new_location = LocationUtility::updateLocation($data, $user, $calendar);
        if ($new_location === false) {
            throw new ServerErrorException('Failed to Update location.');
        }

        // Return the newly updated location
        return $this->translateOutgoingJSON($new_location->id);
    }

    // this is to easily check the users permissions if they can edit calendar's locations
    public function userHasAccessToCalendar(User $user, string $calendar_id)
    {
        $edit_permission = Permission::getByName('Event Edit');
        $create_permission = Permission::getByName('Event Create');

        return $user->hasPermission($edit_permission->id, $calendar_id) &&
            $user->hasPermission($create_permission->id, $calendar_id);
    }

    // This is to find all standard locations
    private function getLocationByStandard(): array
    {
        // Gets main and extension locations
        $main_locations = LocationUtility::getStandardLocations(Location::DISPLAY_ORDER_MAIN);
        $extension_locations = LocationUtility::getStandardLocations(Location::DISPLAY_ORDER_EXTENSION);

        // Gets json for all main locations
        $main_locations_json = array();
        foreach ($main_locations as $location) {
            $main_locations_json[] = $this->translateOutgoingJSON($location->id);
        }

        // Gets json for all extension locations
        $extension_locations_json = array();
        foreach ($extension_locations as $location) {
            $extension_locations_json[] = $this->translateOutgoingJSON($location->id);
        }

        // Returns them as two separate arrays of locations
        return array('main' => $main_locations_json, 'extension' => $extension_locations_json);
    }

    // This is for the regular look up
    private function getLocationById(string $id): array
    {
        return $this->translateOutgoingJSON($id);
    }

    // This is for translating the API json to json that matches the site's form inputs
    private function translateIncomingJSON(array &$location_data)
    {
        $new_location = array();
        $this->replaceJSONKey($location_data, 'id', 'location');

        // Clean up data
        if (isset($location_data['save-user']) && $location_data['save-user'] === 'true') {
            $location_data['location_save'] = 'on';
        }
        if (isset($location_data['save-calendar']) && $location_data['save-calendar'] === 'true') {
            $location_data['location_save_calendar'] = 'on';
        }

        // Replace any non-existing values with empty string for validator
        $new_location['name'] = $location_data['name'] ?? "";
        $new_location['streetaddress1'] = $location_data['address-1'] ?? "";
        $new_location['streetaddress2'] = $location_data['address-2'] ?? "";
        $new_location['city'] = $location_data['city'] ?? "";
        $new_location['state'] = $location_data['state'] ?? "";
        $new_location['zip'] = $location_data['zip'] ?? "";
        $new_location['mapurl'] = $location_data['map-url'] ?? "";
        $new_location['webpageurl'] = $location_data['webpage'] ?? "";
        $new_location['hours'] = $location_data['hours'] ?? "";
        $new_location['phone'] = $location_data['phone'] ?? "";
        $new_location['room'] = $location_data['default-room'] ?? "";
        $new_location['directions'] = $location_data['default-directions'] ?? "";
        $new_location['additionalpublicinfo'] = $location_data['default-additional-public-info'] ?? "";
        $new_location['additionalpublicinfo'] = $location_data['default-additional-public-info'] ?? "";
        $new_location['user_id'] = $location_data['user-id'] ?? "";
        $new_location['calendar_id'] = $location_data['calendar-id'] ?? "";

        // This is to match form inputs
        $location_data['new_location'] = $new_location;
    }

    // Convert location into nicely formatted JSON
    public static function translateOutgoingJSON(string $location_id): array
    {
        // Gets location and checks to make sure it is there
        $location = Location::getByID($location_id);
        if ($location === false) {
            throw new ValidationException('Invalid location ID.');
        }

        // Use location's to JSON function
        $location_json = $location->toJSON();

        // Rename the inputs
        APILocation::replaceJSONKey($location_json, 'location',            'id'                );
        APILocation::replaceJSONKey($location_json, 'location-name',       'name'              );
        APILocation::replaceJSONKey($location_json, 'location-address-1',  'address-1'         );
        APILocation::replaceJSONKey($location_json, 'location-address-2',  'address-2'         );
        APILocation::replaceJSONKey($location_json, 'location-city',       'city'              );
        APILocation::replaceJSONKey($location_json, 'location-state',      'state'             );
        APILocation::replaceJSONKey($location_json, 'location-zip',        'zip'               );
        APILocation::replaceJSONKey($location_json, 'location-map-url',    'map-url'           );
        APILocation::replaceJSONKey($location_json, 'location-webpage',    'webpage'           );
        APILocation::replaceJSONKey($location_json, 'location-hours',      'hours'             );
        APILocation::replaceJSONKey($location_json, 'location-phone',      'phone'             );
        APILocation::replaceJSONKey($location_json, 'location-room',       'default-room'      );
        APILocation::replaceJSONKey($location_json, 'location-directions', 'default-directions');
        APILocation::replaceJSONKey(
            $location_json,
            'location-additional-public-info',
            'default-additional-public-info'
        );
        APILocation::replaceJSONKey($location_json, 'user_id', 'user-id');
        APILocation::replaceJSONKey($location_json, 'calendar_id', 'calendar-id');

        return $location_json;
    }

    // Replace keys in array
    private static function replaceJSONKey(array &$json_data, string $oldKey, string $newKey): void
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
