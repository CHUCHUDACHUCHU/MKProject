<?php
namespace Controller;
use Model\User;

/**
 * AuthController
 * Route에서 컨트롤러 사용.
 * 사용자 인증 관련 액션
 */

class AuthController extends BaseController{
    private User $user;
    private false|array $config;


    public function __construct() {
        $this->user = new User();
        $this->config = parse_ini_file(__DIR__ . '/../config.ini');
    }

    /**
     * 로그인 기능
     * 비밀번호 일치 시 세션 start, 세션에 userIdx 전달
     * 이후 홈화면으로 이동
     */
    public function login() {
        /* body 값 */
        $userEmail = $_POST['userEmail'];
        $userPw = $_POST['userPw'];

        $salt = $this->config['PASSWORD_SALT'];
        $hashPw = crypt($userPw, $salt);

        if($this->parametersCheck($userEmail, $hashPw)) {
            $rst = $this->user->getUserByEmail($userEmail);

            if($rst) {
                if($rst['userPw'] == $hashPw) {
                    // 비밀번호가 일치합니다. 세션을 저장하면 로그인 성공!
                    $_SESSION['userIdx'] = $rst['userIdx'];
                    $_SESSION['userInit'] = $rst['userInit'];

                    // 로깅
                    $this->assembleLogData( userIdx: $rst['userIdx'],
                                            userName: $rst['userName'],
                                            targetClass: get_class($this),
                                            actionFunc: __METHOD__);

                    if($_SESSION['userInit'] === 0) {
                        $this->redirect('/mk-board/user/my-page', '첫 로그인 시 비밀번호 변경이 필요합니다!');
                    } else {
                        $this->redirect('/mk-board/post/list', '');
                    }
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
        $nowUser = $this->user->getUserById($_SESSION['userIdx']);
        // 로깅
        $this->assembleLogData( actionType: "GET",
                                userIdx: $_SESSION['userIdx'],
                                userName: $nowUser['userName'],
                                targetClass: get_class($this),
                                actionFunc: __METHOD__);

        session_unset();
        $this->redirect('/mk-board/auth/login', '');
    }

    /**
     * 세션만료 기능
     * 세션정보와 쿠키값 날려주기.
     */
    public function sessionout() {
        $nowUser = $this->user->getUserById($_SESSION['userIdx']);
        // 로깅
        $this->assembleLogData( actionType: "GET",
                                userIdx: $_SESSION['userIdx'],
                                userName: $nowUser['userName'],
                                targetClass: get_class($this),
                                actionFunc: __METHOD__);

        session_unset();
        setcookie(session_name(), '', time() - 3600, '/');
        $this->redirect('/mk-board/auth/login', '세션이 만료되었습니다!');
    }
}