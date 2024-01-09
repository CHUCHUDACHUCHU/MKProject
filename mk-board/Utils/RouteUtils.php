<?php

namespace Utils;

trait RouteUtils{
    // URL 요청이 해당 경로와 메소드와 일치하는지 확인
    public function routeCheck($origin, $path, $method): bool
    {
        return strpos($origin, $path) !== false
            && $_SERVER['REQUEST_METHOD'] == $method;
    }

    // 뷰 파일을 require 해줌
    public function requireView($viewName): bool
    {
        require_once(__DIR__ . '/../view/' . $viewName . '.php');
        return true;
    }
}

?>