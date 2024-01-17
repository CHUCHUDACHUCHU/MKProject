<?php

namespace Route;


class TestRoute extends BaseRoute {
    function routing($url): bool
    {

        if($this->routeCheck($url, "test/session", "GET")) {
            return $this->requireView('test', 'session');
        } else if($this->routeCheck($url, "test/email", "GET")) {
            return $this->requireView('test', 'email');
        } else if($this->routeCheck($url, "test/show", "GET")) {
            return $this->requireView('test', 'show');
        } else {
            return false;
        }
    }
}