<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\API\NotFoundException;

class AuthAction {
    public $options = array();

    /**
     * @throws NotFoundException
     */
    public function __construct($options = array()) {
        $action = isset($options[0]) ? strtolower($options[0]) : NULL;
        if ($action === 'logout') {
            $this->logout();
        } else {
            // Invalid auth action
            throw new NotFoundException('Not Found');
        }
    }

    private function logout() {
        $auth = new Auth();
        $auth->logout();
        die();
    }
}