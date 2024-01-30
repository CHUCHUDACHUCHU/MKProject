<?php

namespace Utils;

use JetBrains\PhpStorm\Pure;
use Model\Log;

trait RouteUtils{
    // URL 요청이 해당 경로와 메소드와 일치하는지 확인
    #[Pure] public function routeCheck($origin, $path, $method): bool
    {
        return str_contains($origin, $path)
            && $_SERVER['REQUEST_METHOD'] == $method;
    }

    // 뷰 파일을 require 해줌
    public function requireView($directory, $viewName): bool
    {
        require_once(__DIR__ . '/../view/' .$directory. '/' . $viewName . '.php');
        return true;
    }
}

?>