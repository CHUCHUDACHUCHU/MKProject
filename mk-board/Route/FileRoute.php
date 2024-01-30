<?php

namespace Route;
use Controller\FileController;

class FileRoute extends BaseRoute {
    function routing($url): bool
    {
        $FileController = new FileController();

        // 파일 서버 폴더에 업로드 요청
        if($this->routeCheck($url, "file/upload", "POST")) {
            $FileController->fileUpload();
            return true;
            
        // 파일 DB 생성 요청
        } else if($this->routeCheck($url, "file/create", "POST")) {
            $FileController->create();
            return true;
            
        // 파일 데이터와 해당 게시글과 연결 요청
        } else if($this->routeCheck($url, "file/connect", "POST")) {
            $FileController->connectFileWithPost();
            return true;
            
        // 파일 다운로드 요청
        } else if($this->routeCheck($url, "file/download", "POST")) {
            $FileController->download();
            return true;
            
        // 파일 삭제 요청
        } else if($this->routeCheck($url, "file/delete", "POST")) {
            $FileController->delete();
            return true;
        } else {
            return false;
        }
    }
}