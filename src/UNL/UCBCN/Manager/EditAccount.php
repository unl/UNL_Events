<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Account;

class EditAccount
{
    public $options = array();
    public $account;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;

        $user = Auth::getCurrentUser();
        $this->account = $user->getAccount();

        if (!empty($_POST)) {
            $this->updateAccount($_POST);
            Controller::redirect(Controller::getEditAccountURL());
        }
    }

    private function updateAccount($post_data)
    {
        $this->account->name = $post_data['name'];
        $this->account->streetaddress1 = $post_data['address_1'];
        $this->account->streetaddress2 = $post_data['address_2'];
        $this->account->city = $post_data['city'];
        $this->account->state = $post_data['state'];
        $this->account->zip = $post_data['zip'];
        $this->account->fax = $post_data['fax'];
        $this->account->phone = $post_data['phone'];
        $this->account->email = $post_data['email'];
        $this->account->website = $post_data['website'];
        $this->account->sponsor_id = 1;

        $this->account->datelastupdated = date('Y-m-d H:i:s');
        $this->account->update();
    }
}