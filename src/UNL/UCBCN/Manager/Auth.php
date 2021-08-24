<?php

namespace UNL\UCBCN\Manager;
use UNL\UCBCN\User;
use UNL\UCBCN\Account;
use UNL\Templates\Auth\AuthCAS;

class Auth
{
    const UNL_EVENTS_SESSION = 'UNL_EVENTS_SESSION';
    public static $directory_url = 'https://directory.unl.edu/';
    public static $cert_path = '/etc/pki/tls/cert.pem';
    private $auth;

    public function __construct()
    {
        if (!file_exists(self::$cert_path)) {
            self::$cert_path = GuzzleHttp\default_ca_bundle();
        }

        $this->auth = new AuthCAS('2.0', 'shib.unl.edu', 443, '/idp/profile/cas', self::$cert_path, self::UNL_EVENTS_SESSION);
    }

    /**
     * Authenticate the user
     */
    public function authenticate()
    {
        //require login
        $this->auth->login();

        if (!$this->auth->isAuthenticated()) {
            throw new RuntimeException('Unable to authenticate', 403);
        }

        $user = $this->getUser($this->auth->getUserId());

        # check for the account on the user
        if (!$user->getAccount()) {
            # create an account if that account doesn't exist
            $account = new Account;
            $account->name = ucfirst($user->uid) . '\'s Event Publisher';
            $account->sponsor_id = 1;
            $account->datecreated = date('Y-m-d H:i:s');
            $account->datelastupdated = date('Y-m-d H:i:s');

            $account->insert();
        }
    }

    /**
     * Authenticate via token
     */
    public function authenticateViaToken($token)
    {
        return User::getByToken($token);
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        return $this->auth->isAuthenticated();
    }

    /**
     * Check Authenication
     */
    public function checkAuthentication() {
        if (array_key_exists('unl_sso', $_COOKIE) && !$this->auth->isAuthenticated()) {
            // Run PHPCAS checkAuthentication
            $this->auth->checkAuthentication();
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        setcookie(self::UNL_EVENTS_SESSION, "", time() - 3600);
        if ($this->auth->isAuthenticated()) {
            $this->auth->logout();
        }
    }

    public function getCASUserId()
    {
        return $this->auth->getUserId();
    }

    /**
     * Get the current user (will create a user if none exist)
     *
     * @param $uid string the UID of the user
     * @return bool|User
     */
    protected function getUser($uid)
    {
        $uid = trim(strtolower($uid));

        if (empty($uid)) {
            return false;
        }
        if (!$user = User::getByUid($uid)) {
            # create an account for the user
            $account = new Account;
            $account->name = $uid . '\'s Event Publisher';
            $account->sponsor_id = 1;
            $account->datecreated = date('Y-m-d H:i:s');
            $account->datelastupdated = date('Y-m-d H:i:s');
            $account->insert();

            # create this user
            $user = new User();
            $user->uid = $uid;
            $user->account_id = $account->id;
            $user->datecreated = date('Y-m-d H:i:s');
            $user->uidcreated = $uid;
            $user->datelastupdated = date('Y-m-d H:i:s');
            $user->uidlastupdated = $uid;
            $user->insert();
        }

        return $user;
    }

    /**
     * Get a user's information from directory.unl.edu
     *
     * @param string $uid
     * @return array
     */
    public static function getUserInfo($uid)
    {
        $info = array();

        if (!$json = @file_get_contents(self::$directory_url . '?uid=' . $uid . '&format=json')) {
            return $info;
        }

        if (!$json = json_decode($json, true)) {
            return $info;
        }

        $map = array(
            'givenName' => 'first_name',
            'sn' => 'last_name',
            'mail' => 'email'
        );

        foreach ($map as $from => $to) {
            if (isset($json[$from][0])) {
                $info[$to] = $json[$from][0];
            }
        }

        return $info;
    }

    /**
     * Get the currently logged in user if there is one.
     *
     * @return bool|\UNL\UCBCN\User
     */
    public static function getCurrentUser()
    {
        global $_API_USER;

        # check for an API user first
        if (isset($_API_USER)) {
            return $_API_USER;
        }
        $auth = new Auth();
        $userId = $auth->getCASUserId();
        if (!empty($userId)) {
            return User::getByUid($userId);
        }
    }
}
