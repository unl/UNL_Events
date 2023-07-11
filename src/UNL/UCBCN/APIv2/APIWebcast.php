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

        throw new InvalidMethodException('Virtual location only allows get.');
    }

    private function handleGet()
    {
        if (!isset($this->options['webcast_id']) || !is_numeric($this->options['webcast_id'])) {
            throw new ValidationException('Invalid virtual location ID.');
        }

        return $this->translateOutgoingJSON($this->options['webcast_id']);
    }

    private function handlePost(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        if (!isset($data['v_location']) || $data['v_location'] !== 'new') {
            $data['v_location'] = 'new';
        }

        $calendar = null;
        $this->validateIncomingWebcast($data, $user, $calendar);

        $new_webcast = WebcastUtility::addWebcast($data, $user, $calendar);

        if ($new_webcast === false) {
            throw new ServerErrorException('Failed to create virtual location.');
        }

        return $this->translateOutgoingJSON($new_webcast->id);
    }

    private function handlePut(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        if (!isset($data['v_location']) || !is_numeric($data['v_location'])) {
            throw new ValidationException('Invalid Location ID.');
        }

        $webcast = Webcast::getByID($data['v_location']);
        if ($webcast === null) {
            throw new ValidationException('Invalid Location ID');
        }

        if (
            !(isset($webcast->user_id) && $webcast->user_id === $user->uid) &&
            !(isset($webcast->calendar_id) && $this->userHasAccessToCalendar($user, $webcast->calendar_id))
        ) {
            throw new ForbiddenException('You do not have access to modify that virtual location.');
        }

        $calendar = null;
        $this->validateIncomingWebcast($data, $user, $calendar);

        $new_webcast = WebcastUtility::updateWebcast($data, $user, $calendar);

        if ($new_webcast === false) {
            throw new ServerErrorException('Failed to create virtual location.');
        }

        return $this->translateOutgoingJSON($new_webcast->id);
    }

    private function validateIncomingWebcast($data, $user, &$calendar)
    {
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

            if (!$this->userHasAccessToCalendar($user, $calendar->id)) {
                throw new ForbiddenException('You do not have access to that calendar.');
            }
        }

        $validation_data = WebcastUtility::validateWebcast($data);

        if (!$validation_data['valid']) {
            throw new ValidationException($validation_data['message']);
        }
    }

    public function userHasAccessToCalendar(User $user, string $calendar_id)
    {
        $edit_permission = Permission::getByName('Event Edit');
        $create_permission = Permission::getByName('Event Create');

        return $user->hasPermission($edit_permission->id, $calendar_id) &&
            $user->hasPermission($create_permission->id, $calendar_id);
    }

    private function translateIncomingJSON(array &$webcast_data)
    {
        $new_v_location = array();
        $this->replaceJSONKey($webcast_data, 'id', 'v_location');

        if (isset($webcast_data['save-user']) && $webcast_data['save-user'] === 'true') {
            $webcast_data['v_location_save'] = 'on';
        }

        if (isset($webcast_data['save-calendar']) && $webcast_data['save-calendar'] === 'true') {
            $webcast_data['v_location_save_calendar'] = 'on';
        }

        $new_v_location['title'] = $webcast_data['name'] ?? "";
        $new_v_location['url'] = $webcast_data['url'] ?? "";
        $new_v_location['additionalinfo'] = $webcast_data['default-additional-public-info'] ?? "";

        $webcast_data['new_v_location'] = $new_v_location;
    }

    public static function translateOutgoingJSON(string $webcast_id): array
    {
        $webcast = Webcast::getByID($webcast_id);

        if ($webcast === false) {
            throw new ValidationException('Invalid virtual location ID.');
        }

        $webcast_json = $webcast->toJSON();

        APIWebcast::replaceJSONKey($webcast_json, 'v-location',                            'id');
        APIWebcast::replaceJSONKey($webcast_json, 'new-v-location-name',                   'name');
        APIWebcast::replaceJSONKey($webcast_json, 'new-v-location-url',                    'url');
        APIWebcast::replaceJSONKey(
            $webcast_json,
            'new-v-location-additional-public-info',
            'default-additional-public-info'
        );

        return $webcast_json;
    }

    private static function replaceJSONKey(&$json_data, $oldKey, $newKey)
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
