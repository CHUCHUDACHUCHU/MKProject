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
                    // 비밀번호가 일치합니다. 세션을 저장한 후 이동합니다.
                    $_SESSION['userIdx'] = $rst['userIdx'];
                    $_SESSION['userName'] = $rst['userName'];
                    $_SESSION['userEmail'] = $rst['userEmail'];
                    $_SESSION['userPhone'] = $rst['userPhone'];
                    $_SESSION['userDepart'] = $rst['userDepart'];
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

    /**
     * 로그아웃 기능
     * 세션정보만 날려주기.
     */
    public function logout() {
        session_unset();
        $this->redirect('/mk-board/auth/login', '로그아웃합니다!');
    }

    /**
     * 세션만료 기능
     * 세션정보와 쿠키값 날려주기.
     */
    public function session() {
        session_unset();
        setcookie(session_name(), '', time() - 3600, '/');
        $this->redirect('/mk-board/auth/login', '세션이 만료되었습니다!');
    }
}