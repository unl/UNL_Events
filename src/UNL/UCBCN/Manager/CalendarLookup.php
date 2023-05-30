<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Account;

class CalendarLookup extends PostHandler
{
    public $options = array();
    public $account;

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;

        $user = Auth::getCurrentUser();
        $this->account = $user->getAccount();
    }

    public function handlePost(array $get, array $post, array $files)
    {
        //redirect
        return Controller::getUserLocationURL();
    }
}
