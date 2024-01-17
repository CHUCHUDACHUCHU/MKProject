<?php

namespace Route;

use Controller\UserController;

class UserRoute extends BaseRoute {
    function routing($url): bool
    {
        $UserController = new UserController();

        // TODO: Implement routing() method.
        if($this->routeCheck($url, "user/my-page", "GET")) {
            return $this->requireView('user', 'userMypage');
        } else if($this->routeCheck($url, "user/read", "GET")) {
            return $this->requireView('user', 'userRead');
        } else if($this->routeCheck($url, "user/create", "POST")) {
            $UserController->create();
            return true;
        } else if($this->routeCheck($url, "user/update/email", "POST")) {
            $UserController->updateEmail();
            return true;
        } else if($this->routeCheck($url, "user/manage", "GET")) {
            return $this->requireView('user', 'userManage');
        } else if($this->routeCheck($url, "user/emailDupCheck", "GET")) {
            $UserController->getUserByEmail();
            return true;
        } else if($this->routeCheck($url, 'user/send-cert', 'POST')) {
            $UserController->sendCert();
            return true;
        } else if($this->routeCheck($url, 'user/check-cert', 'POST')) {
            $UserController->checkCert();
            return true;
        } else if($this->routeCheck($url, 'user/send-cert/sessionout', 'GET')) {
            $UserController->sessionout();
            return true;
        } else {
            return false;
        }
    }
}