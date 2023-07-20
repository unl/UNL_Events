<?php

namespace UNL\UCBCN\APIv2;

interface ModelAuthInterface
{
    public function needsAuth (string $method): bool;
    public function canUseTokenAuth(string $method): bool;
    public function canUseCookieAuth(string $method): bool;
}
