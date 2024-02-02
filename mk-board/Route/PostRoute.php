<?php

namespace Route;

use Controller\PostController;

class PostRoute extends BaseRoute {
    function routing($url): bool
    {
        $PostController = new PostController();

        // 메인 리스트 뷰페이지 요청
        if($this->routeCheck($url, "post/list", "GET")) {
            return $this->requireView('post', 'main');
            
        // 게시글 상세 뷰페이지 요청
        } else if ($this->routeCheck($url, "post/read", "GET")) {
            return $this->requireView('post', 'postRead');
            
        // 게시글 작성 뷰페이지 요청
        } else if ($this->routeCheck($url, "post/create", "GET")) {
            return $this->requireView('post', 'postCreate');
            
        // 게시글 수정 뷰페이지 요청
        } else if ($this->routeCheck($url, "post/update", "GET")) {
            return $this->requireView('post', 'postUpdate');
            
        // 게시글 관리 뷰페이지 요청
        } else if ($this->routeCheck($url, "post/manage", "GET")) {
            return $this->requireView('post', 'postManage');
        }


        // 게시글 생성 요청
        else if ($this->routeCheck($url, "post/create/fetch", "POST")) {
            $PostController->createByFetch();
            return true;

        // 게시글 수정 요청(파일 업로드 시 : 추후 Form, Fetch 합치는 refactoring 필요)
        } else if ($this->routeCheck($url, "post/update/fetch", "POST")) {
            $PostController->updateByFetch();
            return true;
            
        // 게시글 상태 변경 요청
        } else if ($this->routeCheck($url, "post/update/status", "POST")) {
            $PostController->updateStatus();
            return true;
            
        // 게시글 삭제 요청
        } else if ($this->routeCheck($url, "post/delete", "POST")) {
            $PostController->delete();
            return true;
        }else {
            return false;
        }
    }
}