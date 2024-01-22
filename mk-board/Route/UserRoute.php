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
        } else if($this->routeCheck($url, "user/code/status", "POST")) {
            $UserController->userCodeStatus();
            return true;
        } else if($this->routeCheck($url, "user/create", "POST")) {
            $UserController->create();
            return true;
        } else if($this->routeCheck($url, "user/delete", "POST")) {
            $UserController->delete();
            return true;
        } else if($this->routeCheck($url, "user/update/all", "POST")) {
            $UserController->updateAll();
            return true;
        } else if($this->routeCheck($url, "user/update/email", "POST")) {
            $UserController->updateEmail();
            return true;
        } else if($this->routeCheck($url, "user/update/password", "POST")) {
            $UserController->updatePassword();
            return true;
        } else if($this->routeCheck($url, "user/update/status", "POST")) {
            $UserController->updateStatus();
            return true;
        } else if($this->routeCheck($url, "user/reset/password", "POST")) {
            $UserController->resetPassword();
            return true;
        } else if($this->routeCheck($url, "user/manage", "GET")) {
            return $this->requireView('user', 'userManage');
        } else if($this->routeCheck($url, 'user/code/send', 'POST')) {
            $UserController->codeSend();
            return true;
        } else if($this->routeCheck($url, 'user/code/check', 'POST')) {
            $UserController->codeCheck();
            return true;
        } else if($this->routeCheck($url, 'user/code/sessionout', 'GET')) {
            $UserController->sessionout();
            return true;
        } else {
            return false;
        }
    }
}