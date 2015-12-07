<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;

class EditUserInfo extends PostHandler
{
    public $options = array();
    public $user;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->user = Auth::getCurrentUser();
    }

    private function generateAPIToken()
    {
        $token = '';
        do {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $characters_length = strlen($characters);
            $token = '';
            for ($i = 0; $i < 20; $i++) {
                $token .= $characters[rand(0, $characters_length - 1)];
            }

        } while (User::getByToken($token) !== FALSE);

        $this->user->token = $token;
        $this->user->update();
    }

    public function handlePost(array $get, array $post, array $files)
    {
        if (array_key_exists('generate_api_token', $post)) {
            $this->generateAPIToken();
            $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'API Token Generated', 'A new API token has been generated.');
        }

        //redirect
        return Controller::getEditMeURL();
    }
}