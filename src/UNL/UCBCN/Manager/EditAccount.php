<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Account;

class EditAccount implements PostHandlerInterface
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
            'address_1',
            'address_2',
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
        
        //redirect
        return Controller::getEditAccountURL();
    }
}