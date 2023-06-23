<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Calendar\EventTypes as EventTypes;

class APIEventTypes implements ModelInterface
{
    public $options = array();

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
    }

    public function run(string $method, array $data, $user): array
    {
        if ($method !== 'GET') {
            throw new InvalidMethodException('Event type only allows get.');
        }

        $output_data = array();
        $all_event_types = new EventTypes();

        foreach($all_event_types as $audience) {
            $output_data[] = $audience->name;
        }

        return array('eventtypes' => $output_data);
    }
}
