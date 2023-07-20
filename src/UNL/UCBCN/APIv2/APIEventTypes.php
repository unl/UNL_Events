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

    // This is only for getting list of all the audiences
    public function run(string $method, array $data, $user): array
    {
        if ($method !== 'GET') {
            throw new InvalidMethodException('Event type only allows get.');
        }

        $output_data = array();
        $all_event_types = new EventTypes();

        // Clean up data
        foreach ($all_event_types as $event_type) {
            $output_data[] = array(
                'id' => $event_type->id,
                'name' => $event_type->name,
            );
        }

        // Order by id asc
        usort($output_data, function($a, $b) {return intval($a['id']) > intval($b['id']);});

        return array('eventtypes' => $output_data);
    }
}
