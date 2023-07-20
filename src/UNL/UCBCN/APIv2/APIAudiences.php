<?php
namespace UNL\UCBCN\APIv2;

use UNL\UCBCN\Calendar\Audiences as Audiences;

class APIAudiences implements ModelInterface
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
            throw new InvalidMethodException('Audience only allows get.');
        }

        $output_data = array();
        $all_audiences = new Audiences();

        // Clean up data
        foreach ($all_audiences as $audience) {
            $output_data[] = array(
                'id' => $audience->id,
                'name' => $audience->name,
            );
        }

        // Order by id asc
        usort($output_data, function($a, $b) {return intval($a['id']) > intval($b['id']);});

        return array('audiences' => $output_data);
    }
}
