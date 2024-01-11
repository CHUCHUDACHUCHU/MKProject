<?php
namespace Controller;

use Model\User;

class UserController extends BaseController {
    private $user;

    public function __construct() {
        $this->user = new user();
    }

    public function create() {
        $userName = $_POST['userName'];
        $userEmail = $_POST['userEmail'];
        $userPw = $_POST['userPw'];
        $userDepart = $_POST['userDepart'];
        $userPhone = $_POST['userPhone'];


//        $salt = '$5$QOPrAVIK'."$userEmail".'$';
//        $hashPw = crypt($userPw, $salt);

        if($this->parametersCheck($userName, $userEmail, $userPw, $userDepart, $userPhone)) {
            if($this->user->create($userName, $userEmail, $userPw, $userDepart, $userPhone)) {
                $this->redirect('/mk-board', '회원이 생성되었습니다.');
            } else {
                $this->redirectBack('회원이 생성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }
}