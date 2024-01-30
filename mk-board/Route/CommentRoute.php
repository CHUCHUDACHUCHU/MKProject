<?php

namespace Route;

use Controller\CommentController;

class CommentRoute extends BaseRoute {
    function routing($url): bool
    {
        
        $CommentController = new CommentController();

        // 댓글 생성 요청
        if($this->routeCheck($url, "comment/create", "POST")) {
            $CommentController->create();
            return true;
            
        // 댓글 생성 요청
        } else if($this->routeCheck($url, "comment/update", "POST")) {
            $CommentController->update();
            return true;
            
        // 댓글 삭제 요청
        } else if($this->routeCheck($url, "comment/delete", "POST")) {
            $CommentController->delete();
            return true;
        } else {
            return false;
        }
    }
}