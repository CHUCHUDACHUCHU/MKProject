<?php

namespace Route;

use Controller\UserController;

class UserRoute extends BaseRoute {
    function routing($url): bool
    {
        $UserController = new UserController();

        // TODO: Implement routing() method.
        if($this->routeCheck($url, "user/read", "GET")) {
            return $this->requireView('userRead');
        } else if($this->routeCheck($url, "user/create", "POST")) {
            $UserController->create();
            return true;
        }else {
            return false;
        }
    }
}