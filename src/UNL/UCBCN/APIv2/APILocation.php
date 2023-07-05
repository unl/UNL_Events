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
            return $this->handleGet();
        }
        if ($method === 'POST') {
            return $this->handlePost($data, $user);
        }
        if ($method === 'PUT') {
            return $this->handlePut($data, $user);
        }

        throw new InvalidMethodException('Location only allows get.');
    }

    private function handleGet(): array
    {
        if (
            !isset($this->options['location_id']) ||
            (!is_numeric($this->options['location_id']) && $this->options['location_id'] !== 'standard')
        ) {
            throw new ValidationException('Invalid location ID.');
        }

        if (is_numeric($this->options['location_id'])) {
            return $this->getLocationById($this->options['location_id']);
        } elseif ($this->options['location_id'] === 'standard') {
            return $this->getLocationByStandard();
        }

        throw new ValidationException('Invalid location ID.');
    }

    private function handlePost(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        if (!isset($data['location']) || $data['location'] !== 'new') {
            $data['location'] = 'new';
        }

        $calendar = null;
        if (isset($data['location_save_calendar']) && $data['location_save_calendar'] === 'on') {
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

            if (!$this->userHasAccessToCalendar($user, $calendar->id)) {
                throw new ForbiddenException('You do not have access to that calendar.');
            }
        }

        $validation_data = LocationUtility::validateLocation($data);

        if (!$validation_data['valid']) {
            throw new ValidationException($validation_data['message']);
        }

        $new_location = LocationUtility::addLocation($data, $user, $calendar);

        if ($new_location === false) {
            throw new ServerErrorException('Failed to create location.');
        }

        return $this->translateOutgoingJSON($new_location->id);
    }

    private function handlePut(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        if (!isset($data['location']) || !is_numeric($data['location'])) {
            throw new ValidationException('Invalid Location ID.');
        }

        $location = Location::getByID($data['location']);
        if ($location === null) {
            throw new ValidationException('Invalid Location ID');
        }

        if (
            !(isset($location->user_id) && $location->user_id === $user->uid) &&
            !(isset($location->calendar_id) && $this->userHasAccessToCalendar($user, $location->calendar_id))
        ) {
            throw new ForbiddenException('You do not have access to modify that location.');
        }

        $calendar = null;
        if (isset($data['location_save_calendar']) && $data['location_save_calendar'] === 'on') {
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

            if (!$this->userHasAccessToCalendar($user, $calendar->id)) {
                throw new ForbiddenException('You do not have access to that calendar.');
            }
        }

        $validation_data = LocationUtility::validateLocation($data);

        if (!$validation_data['valid']) {
            throw new ValidationException($validation_data['message']);
        }

        $new_location = LocationUtility::updateLocation($data, $user, $calendar);

        if ($new_location === false) {
            throw new ServerErrorException('Failed to Update location.');
        }

        return $this->translateOutgoingJSON($new_location->id);;
    }

    public function userHasAccessToCalendar(User $user, string $calendar_id)
    {
        $edit_permission = Permission::getByName('Event Edit');
        $create_permission = Permission::getByName('Event Create');

        return $user->hasPermission($edit_permission->id, $calendar_id) &&
            $user->hasPermission($create_permission->id, $calendar_id);
    }

    private function getLocationByStandard(): array
    {
        $main_locations = LocationUtility::getStandardLocations(Location::DISPLAY_ORDER_MAIN);
        $extension_locations = LocationUtility::getStandardLocations(Location::DISPLAY_ORDER_EXTENSION);

        $main_locations_json = array();
        $extension_locations_json = array();

        foreach ($main_locations as $location) {
            $main_locations_json[] = $this->translateOutgoingJSON($location->id);;
        }

        foreach ($extension_locations as $location) {
            $extension_locations_json[] = $this->translateOutgoingJSON($location->id);;
        }

        return array('main' => $main_locations_json, 'extension' => $extension_locations_json);
    }

    private function getLocationById(string $id): array
    {
        return $this->translateOutgoingJSON($id);
    }

    private function translateIncomingJSON(array &$location_data)
    {
        $new_location = array();
        $this->replaceJSONKey($location_data, 'id', 'location');

        if (isset($location_data['save-user']) && $location_data['save-user'] === 'true') {
            $location_data['location_save'] = 'on';
        }

        if (isset($location_data['save-calendar']) && $location_data['save-calendar'] === 'true') {
            $location_data['location_save_calendar'] = 'on';
        }

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

        $location_data['new_location'] = $new_location;
    }

    public static function translateOutgoingJSON(string $location_id): array
    {
        $location = Location::getByID($location_id);

        if ($location === false) {
            throw new ValidationException('Invalid location ID.');
        }

        $location_json = $location->toJSON();

        APILocation::replaceJSONKey($location_json, 'location',                        'id'                            );
        APILocation::replaceJSONKey($location_json, 'location-name',                   'name'                          );
        APILocation::replaceJSONKey($location_json, 'location-address-1',              'address-1'                     );
        APILocation::replaceJSONKey($location_json, 'location-address-2',              'address-2'                     );
        APILocation::replaceJSONKey($location_json, 'location-city',                   'city'                          );
        APILocation::replaceJSONKey($location_json, 'location-state',                  'state'                         );
        APILocation::replaceJSONKey($location_json, 'location-zip',                    'zip'                           );
        APILocation::replaceJSONKey($location_json, 'location-map-url',                'map-url'                       );
        APILocation::replaceJSONKey($location_json, 'location-webpage',                'webpage'                       );
        APILocation::replaceJSONKey($location_json, 'location-hours',                  'hours'                         );
        APILocation::replaceJSONKey($location_json, 'location-phone',                  'phone'                         );
        APILocation::replaceJSONKey($location_json, 'location-room',                   'default-room'                  );
        APILocation::replaceJSONKey($location_json, 'location-directions',             'default-directions'            );
        APILocation::replaceJSONKey($location_json, 'location-additional-public-info', 'default-additional-public-info');

        return $location_json;
    }

    private static function replaceJSONKey(array &$json_data, string $oldKey, string $newKey): void
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
