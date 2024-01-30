<?php

namespace Route;

use Controller\AuthController;

class AuthRoute extends BaseRoute {
    function routing($url): bool
    {
        // TODO: Implement routing() method.
        $AuthController = new AuthController();

        // 로그인 뷰페이지 요청
        if($this->routeCheck($url, "auth/login", "GET")) {
            return $this->requireView('auth', 'authLogin');
            
        // 로그아웃 요청
        } else if($this->routeCheck($url, "auth/logout", "GET")) {
            $AuthController->logout();
            return true;
            
        // 세션만료 요청
        } else if($this->routeCheck($url, "auth/sessionout", "GET")) {
            $AuthController->sessionout();
            return true;
            
        // 로그인 요청
        } else if($this->routeCheck($url, "auth/login", "POST")) {
            $AuthController->login();
            return true;
        } else {
            return false;
        }
    }
}