<?php

namespace Route;
use Controller\TestController;

class LogRoute extends BaseRoute {
    function routing($url): bool
    {
        if($this->routeCheck($url, "log/manage", "GET")) {
            return $this->requireView('log', 'logRead');
        }  else {
            return false;
        }
    }
}