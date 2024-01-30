<?php

namespace Route;
use Controller\FileController;

class FileRoute extends BaseRoute {
    function routing($url): bool
    {
        $FileController = new FileController();

        if($this->routeCheck($url, "file/upload", "POST")) {
            $FileController->fileUpload();
            return true;
        } else if($this->routeCheck($url, "file/create", "POST")) {
            $FileController->create();
            return true;
        } else if($this->routeCheck($url, "file/connect", "POST")) {
            $FileController->connectFileWithPost();
            return true;
        } else if($this->routeCheck($url, "file/download", "POST")) {
            $FileController->download();
            return true;
        } else if($this->routeCheck($url, "file/delete", "POST")) {
            $FileController->delete();
            return true;
        } else {
            return false;
        }
    }
}