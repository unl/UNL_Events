<?php

namespace UNL\UCBCN\APIv2;

//TODO: add options HTTP method for letting the user know their available options for routes
interface ModelInterface
{
    public function run(string $method, array $data, $user): array;
}
