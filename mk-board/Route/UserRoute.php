<?php

namespace Route;

use Controller\UserController;

class UserRoute extends BaseRoute {
    function routing($url): bool
    {
        $UserController = new UserController();

        // 회원 마이페이지 뷰페이지 요청
        if($this->routeCheck($url, "user/my-page", "GET")) {
            return $this->requireView('user', 'userMypage');
            
        // 특정회원 페이지 뷰페이지 요청
        } else if($this->routeCheck($url, "user/read", "GET")) {
            return $this->requireView('user', 'userRead');

        // 회원 관리 뷰페이지 요청
        } else if($this->routeCheck($url, "user/manage", "GET")) {
            return $this->requireView('user', 'userManage');
        }
        

        // 인증번호 보내기 전 상태 체크 요청
        else if($this->routeCheck($url, "user/code/status", "POST")) {
            $UserController->userCodeStatus();
            return true;
            
        // 실제 인증번호 보내기 요청
        } else if($this->routeCheck($url, 'user/code/send', 'POST')) {
            $UserController->codeSend();
            return true;
            
        // 인증번호 유효 체크 요청
        } else if($this->routeCheck($url, 'user/code/check', 'POST')) {
            $UserController->codeCheck();
            return true;
            
        // 인증번호 세션 만료 요청
        } else if($this->routeCheck($url, 'user/code/sessionout', 'GET')) {
            $UserController->sessionout();
            return true;
            
        // 회원 계정 생성 요청
        }  else if($this->routeCheck($url, "user/create", "POST")) {
            $UserController->create();
            return true;

        // 회원 권한 변경 요청
        } else if($this->routeCheck($url, "user/update/status", "POST")) {
            $UserController->updateStatus();
            return true;
            
        // 회원 계정 삭제 요청
        } else if($this->routeCheck($url, "user/delete", "POST")) {
            $UserController->delete();
            return true;
            
        // 회원 개인정보 수정 요청
        } else if($this->routeCheck($url, "user/update/info", "POST")) {
            $UserController->updateMyInfo();
            return true;
            
        // 회원 이메일 수정 요청
        } else if($this->routeCheck($url, "user/update/email", "POST")) {
            $UserController->updateEmail();
            return true;
            
        // 회원 비밀번호 수정 요청
        } else if($this->routeCheck($url, "user/update/password", "POST")) {
            $UserController->updatePassword();
            return true;
            
        // 회원 비밀번호 초기화 요청
        } else if($this->routeCheck($url, "user/reset/password", "POST")) {
            $UserController->resetPassword();
            return true;
        } else {
            return false;
        }
    }
}