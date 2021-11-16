<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Account;

class EditAccount extends PostHandler
{
    public $options = array();
    public $account;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;

        $user = Auth::getCurrentUser();
        $this->account = $user->getAccount();
    }

    private function updateAccount($post_data)
    {
        $allowed_fields = array(
            'name',
            'streetaddress1',
            'streetaddress2',
            'city',
            'state',
            'zip',
            'fax',
            'phone',
            'email',
            'website'
        );
        
        //Update fields
        foreach ($allowed_fields as $field) {
            $this->account->$field = $post_data[$field];
        }
        
        //Update non-editable fields
        $this->account->sponsor_id      = 1;
        $this->account->datelastupdated = date('Y-m-d H:i:s');
        
        //perform the update
        $this->account->update();
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->updateAccount($post);
        
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Account Updated', 'Your UNL Events account has been updated.');
        //redirect
        return Controller::getEditAccountURL();
    }
}