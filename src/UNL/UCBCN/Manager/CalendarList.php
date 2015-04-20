<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;

class CalendarList {
    public function getCalendars() {
        $user = Auth::getCurrentUser();

        return $user->getCalendars();
    }
}