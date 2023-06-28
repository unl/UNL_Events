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

        $this->calendar_create = $this->endsWith($_SERVER['REQUEST_URI'], '/calendar') || $this->endsWith($_SERVER['REQUEST_URI'], '/calendar/');

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
            return $this->calendarToJSON($this->calendar);
        }

        if ($method === 'POST') {
            return $this->handlePost($data, $user);
        }

        if ($method === 'PUT') {
            return $this->handlePut($data, $user);
        }

        if ($method === 'DELETE') {
            $delete_permission = Permission::getByName('Calendar Delete');
            if (!$user->hasPermission($delete_permission->id, $this->calendar->id)) {
                throw new ForbiddenException('You do not have access to delete this calendar.');
            }
            $this->calendar->delete();
            return array($this->calendar->name . ' has been deleted.');
        }

        throw new InvalidMethodException('Calendar only allows get.');
    }

    private function handlePost(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        $createCalendar = new CreateCalendar(array(
            'user' => $user,
        ));

        try {
            $createCalendar->createCalendar($data);
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->calendarToJSON($createCalendar->calendar);
    }

    private function handlePut(array $data, User $user): array
    {
        $this->translateIncomingJSON($data);

        try {
            $createCalendar = new CreateCalendar(array(
                'calendar_shortname' => $this->calendar->shortname,
                'user' => $user,
            ));
        } catch(\Exception $e) {
            if ($e->getCode() === 403) {
                throw new ForbiddenException('You can not edit that calendar.');
            } else {
                throw new ValidationException($e->getMessage());
            }
        }
        

        try {
            $createCalendar->updateCalendar($data);
        } catch (\UNL\UCBCN\Manager\ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        return $this->calendarToJSON($createCalendar->calendar);
    }

    private function calendarToJSON(calendar $calendar): array
    {
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
        }

        $recommendations_within_account = $calendar->recommendationswithinaccount === '1';

        return array(
            'id' => $calendar->id,
            'name' => $calendar->name,
            'short-name' => $calendar->shortname,
            'default-timezone' => $this->translateTimezone($calendar->defaulttimezone),
            'website' => $calendar->website,
            'email-list' => $calendar->emaillists,
            'event-release-preference' => $event_release_preference,
            'recommendations-within-account' => $recommendations_within_account,
        );
    }

    private function translateIncomingJSON(array &$calendar_data)
    {
        $this->replaceJSONKey($calendar_data, 'short-name', 'shortname');
        $this->replaceJSONKey($calendar_data, 'email-lists', 'email_lists');
        $this->replaceJSONKey($calendar_data, 'recommend-within-account', 'recommend_within_account');

        $calendar_data['event_release_preference'] = strtolower($calendar_data['event-release-preference']);

        $timezones = BaseUCBCN::getTimezoneOptions();
        $calendar_data['default-timezone'] = ucfirst(strtolower($calendar_data['default-timezone']));

        if (!array_key_exists($calendar_data['default-timezone'], $timezones)) {
            throw new ValidationException('Invalid timezone');
        }

        $calendar_data['defaulttimezone'] = $timezones[$calendar_data['default-timezone']];
    }

    private function translateTimezone($phpTimeZone)
    {
        $timezones = BaseUCBCN::getTimezoneOptions();

        return array_search($phpTimeZone, $timezones);
    }

    private function endsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        if( !$length ) {
            return true;
        }
        return substr( $haystack, -$length ) === $needle;
    }

    private function replaceJSONKey(array &$json_data, string $oldKey, string $newKey): void
    {
        if (!key_exists($oldKey, $json_data)) {
            return;
        }
        $json_data[$newKey] = $json_data[$oldKey];
        unset($json_data[$oldKey]);
    }
}
