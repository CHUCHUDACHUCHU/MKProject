<?php

namespace Route;

use Controller\AuthController;

class AuthRoute extends BaseRoute {
    function routing($url): bool
    {
        // TODO: Implement routing() method.
        $AuthController = new AuthController();

        if($this->routeCheck($url, "auth/login", "GET")) {
            return $this->requireView('auth', 'authLogin');
        } else if($this->routeCheck($url, "auth/logout", "GET")) {
            $AuthController->logout();
            return true;
        } else if($this->routeCheck($url, "auth/sessiondout", "GET")) {
            $AuthController->sessionout();
            return true;
        } else if($this->routeCheck($url, "auth/login", "POST")) {
            $AuthController->login();
            return true;
        } else {
            return false;
        }
    }
}