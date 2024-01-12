<?php

namespace Route;

use Controller\PostController;

class PostRoute extends BaseRoute {
    function routing($url): bool
    {
        $PostController = new PostController();

        // TODO: Implement routing() method.
        if($this->routeCheck($url, "post/list", "GET")) {
            return $this->requireView('main');
        } else if ($this->routeCheck($url, "post/read", "GET")) {
            return $this->requireView('postRead');
        } else if ($this->routeCheck($url, "post/create", "GET")) {
            return $this->requireView('postCreate');
        } else if ($this->routeCheck($url, "post/update", "GET")) {
            return $this->requireView('postUpdate');
        } else if ($this->routeCheck($url, "post/create", "POST")) {
            $PostController->create();
            return true;
        } else if ($this->routeCheck($url, "post/update", "POST")) {
            $PostController->update();
            return true;
        } else {
            return false;
        }
    }
}