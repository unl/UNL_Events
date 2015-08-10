<?php

namespace UNL\UCBCN\Manager;
use SimpleCAS;
use UNL\UCBCN\User;
use UNL\UCBCN\Account;
use UNL\UCBCN\Calendar;

class Auth {

    protected $options = array();
    
    public static $directory_url = 'http://directory.unl.edu/';

    /**
     * Authenticate the user
     */
    public function authenticate() {
        $client = $this->getClient();
        
        //Handle single log out requests
        $client->handleSingleLogOut();
        
        //require login
        $client->forceAuthentication();
        
        if (!$client->isAuthenticated()) {
            throw new RuntimeException('Unable to authenticate', 403);
        }
        
        $user = $this->getUser($client->getUsername());

        # check for the account on the user
        if (!$user->getAccount()) {
            # create an account if that account doesn't exist
            $account = new Account;
            $account->name = ucfirst($user->uid).'\'s Event Publisher';
            $account->sponsor_id = 1;
            $account->datecreated = date('Y-m-d H:i:s');
            $account->datelastupdated = date('Y-m-d H:i:s');

            $account->insert();
        }
    }

    /**
     * Get the current user (will create a user if none exist)
     *
     * @param $uid string the UID of the user
     * @return bool|User
     */
    protected function getUser($uid) {
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

            # create a base calendar for this user
            $calendar = new Calendar;
            $calendar->name = $user->uid .'\'s Event Publisher';
            $calendar->shortname = $user->uid;
            $calendar->uidcreated = $user->uid;
            $calendar->uidlastupdated = $user->uid;
            $calendar->datecreated = date('Y-m-d H:i:s');
            $calendar->datelastupdated = date('Y-m-d H:i:s');
            $calendar->account_id = $account->id;
            $calendar->insert();
            $calendar->addUser($user);

            $user->calendar_id = $calendar->id;
            $user->update();
        }
        
        return $user;
    }

    public function getClient() {
        $options = array(
            'hostname' => 'login.unl.edu',
            'port'     => 443,
            'uri'      => 'cas'
        );
        
        $protocol = new \SimpleCAS_Protocol_Version2($options);
        /**
         * We need to customize the request to use CURL because 
         * php5.4 and ubuntu systems can't verify ssl connections 
         * without specifying a CApath.  CURL does this automatically
         * based on the system, but openssl does not.
         * 
         * It looks like this will be fixed in php 5.6
         * https://wiki.php.net/rfc/tls-peer-verification
         */
        $request = new \HTTP_Request2();
        $request->setConfig('adapter', 'HTTP_Request2_Adapter_Curl');
        $protocol->setRequest($request);
        /**
         * Set up the session cache mapping
         */
        $cache_driver = new \Stash\Driver\FileSystem();
        $cache_driver->setOptions(array(
            //Scope the cache to the current application only.
            'path' => '/tmp/simpleCAS_map',
        ));
        
        $session_map = new \SimpleCAS_SLOMap($cache_driver);
        
        $protocol->setSessionMap($session_map);
        return \SimpleCAS::client($protocol);
    }

    /**
     * Get a user's information from directory.unl.edu
     * 
     * @param string $uid
     * @return array
     */
    public static function getUserInfo($uid) {
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
        if (!isset($_SESSION['__SIMPLECAS']['UID'])) {
            return false;
        }
        
        $username = $_SESSION['__SIMPLECAS']['UID'];
        return User::getByUid($username);
    }
}