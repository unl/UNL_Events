<?php
namespace UNL\UCBCN\APIv2;

class APIHome implements ModelInterface
{
    public $options = array();

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    // This is to give a detailed list of all the routes available at the time
    public function run(string $method, array $data, $user): array
    {
        $url_base = \UNL\UCBCN\Frontend\Controller::$url . 'api/v2';
        return array(
            'GET' => array(
                'routes' => array(
                    $url_base . '/audiences',
                    $url_base . '/eventtypes',
                    $url_base . '/location/standard',
                    $url_base . '/location/{location_id}',
                    $url_base . '/virtual-location/{virtual_location_id}',
                    $url_base . '/calendar/{calendar_id}',
                    $url_base . '/calendar/{calendar_id}/event/{event_id}',
                    $url_base . '/calendar/{calendar_id}/event/datetime/{datetime_id}',
                    $url_base . '/calendar/{calendar_id}/event/datetime/recurrence/{recurrence_id}',
                    $url_base . '/calendar/{calendar_id}/events',
                    $url_base . '/calendar/{calendar_id}/events/search',
                    $url_base . '/calendar/{calendar_id}/events/location/{location_id}',
                    $url_base . '/calendar/{calendar_id}/events/virtual-location/{virtual_location_id}',
                ),
                'cookie_auth_routes' => array(
                    $url_base . '/me',
                    $url_base . '/me/locations',
                    $url_base . '/me/virtual-locations',
                ),
                'auth_required_routes' => array(
                    $url_base . '/calendar/{calendar_id}/events/pending',
                    $url_base . '/me',
                    $url_base . '/me/locations',
                    $url_base . '/me/virtual-locations',
                )
            ),
            'POST' => array(
                'auth_required_routes' => array(
                    $url_base . '/location',
                    $url_base . '/virtual-location',
                    $url_base . '/calendar',
                    $url_base . '/calendar/{calendar_id}/event',
                ),
            ),
            'PUT' => array(
                'auth_required_routes' => array(
                    $url_base . '/location/{location_id}',
                    $url_base . '/virtual-location/{virtual_location_id}',
                    $url_base . '/calendar/{calendar_id}',
                    $url_base . '/calendar/{calendar_id}/event/{event_id}',
                ),
            ),
            'DELETE' => array(
                'auth_required_routes' => array(
                    $url_base . '/calendar/{calendar_id}',
                    $url_base . '/calendar/{calendar_id}/event/{event_id}',
                ),
            ),
        );
    }
}
