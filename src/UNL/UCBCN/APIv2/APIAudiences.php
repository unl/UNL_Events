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

    public function run(string $method, array $data, $user): array
    {
        if ($method !== 'GET') {
            throw new InvalidMethodException('Audience only allows get.');
        }

        $output_data = array();
        $all_audiences = new Audiences();

        foreach($all_audiences as $audience) {
            $output_data[] = $audience->name;
        }

        return array('audiences' => $output_data);
    }
}
