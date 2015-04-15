<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;

class CalendarList {
	public function getCalendars() {
    	$username = $_SESSION['__SIMPLECAS']['UID'];
    	$user = User::getByUid($username);

    	return $user->getCalendars();
    }
}