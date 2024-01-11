<?php
namespace Controller;
use Model\User;

/**
 * AuthController
 * Route에서 컨트롤러 사용.
 * 사용자 인증 관련 액션
 */

class AuthController extends BaseController{
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    /**
     * 로그인 기능
     * 비밀번호 일치 시 세션 start, 세션에 userIdx 전달
     * 이후 홈화면으로 이동
     */
    public function login() {
        $userEmail = $_POST['userEmail'];
        $userPw = $_POST['userPw'];

        if($this->parametersCheck($userEmail, $userPw)) {
            $rst = $this->user->getUserByEmail($userEmail);

            if($rst) {
                if($rst['userPw'] == $userPw) {
                    // 비밀번호가 일치합니다. 세션을 저장한 후 /mk-board/post/list로 이동합니다.
                    session_start();
                    $_SESSION['userIdx'] = $rst['userIdx'];
                    $this->redirect('/mk-board/post/list', '로그인합니다!');
                } else {
                    $this->redirectBack("비밀번호가 일치하지 않습니다.");
                }
            } else {
                // 등록된 이메일이 없습니다.
                $this->redirectBack('등록된 이메일이 없습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }
}