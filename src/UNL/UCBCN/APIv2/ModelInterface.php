<?php

namespace UNL\UCBCN\APIv2;

interface ModelInterface
{
    public function run(string $method, array $data): array;
}
