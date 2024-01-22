<?php

namespace Route;

use Controller\CommentController;

class CommentRoute extends BaseRoute {
    function routing($url): bool
    {
        // TODO: Implement routing() method.
        $CommentController = new CommentController();

        if($this->routeCheck($url, "comment/create", "POST")) {
            $CommentController->create();
            return true;
        } else if($this->routeCheck($url, "comment/update", "POST")) {
            $CommentController->update();
            return true;
        } else if($this->routeCheck($url, "comment/delete", "POST")) {
            $CommentController->delete();
            return true;
        } else {
            return false;
        }
    }
}