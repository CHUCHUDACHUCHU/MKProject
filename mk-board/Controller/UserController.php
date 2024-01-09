<?php
//namespace MKBoard\Controller;
//
//use MKBoard\Model\User;
//
//class UserController extends BaseController {
//    private $user;
//
//    public function __construct() {
//        $this->user = new user();
//    }
//
//    public function createUser() {
//        $userName = $_POST['userName'];
//        $userEmail = $_POST['userEmail'];
//        $userPw = $_POST['userPw'];
//        $userDepart = $_POST['userDepart'];
//        $userPhone = $_POST['userPhone'];
//
//        if($this->parametersCheck($userName, $userEmail, $userPw, $userDepart, $userPhone)) {
//            if($this->user->create($userName, $userEmail, $userPw, $userDepart, $userPhone)) {
//                $this->redirect('/main.php', '회원이 생성되었습니다.');
//            } else {
//                $this->redirectBack('글 작성에 실패했습니다.');
//            }
//        } else {
//            $this->redirectBack('입력되지 않은 값이 있습니다.');
//        }
//    }
//
//    // 회원 이메일 중복 검사 모델매칭
//    public function checkUserEmail() {
//        $userEmail = $_GET['userEmail'];
//
//        if($this->parametersCheck($userEmail)) {
//            $this->echoJson($this->user->getUser($userEmail));
//        } else {
//            $this->echoJson(['result' => false, 'msg' => '입력 값이 올바르지 않습니다.']);
//        }
//    }
//}