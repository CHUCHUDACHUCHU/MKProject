<?php
namespace Controller;
use Model\User;

/**
 * AccessController
 * 모든 라우팅이 일어나기 전
 * 접근 제한 필터링이 일어나는 곳.
 */

class AccessController extends BaseController{
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    /**
     * 접근 제한 필터링
     * @param $url
     */
    public function accessFilter($url) {
        // 1. 기본 url 입력 시
        // redirect auth/login

        // 2. 로그인 했는데 로그인 화면 요청 시
        // redirect post/list


        // 3. 로그인 안 했을 시 : $_SESSION['userIdx'] 가 없을 때
        // 가능   :   로그인 화면요청, 로그인 요청, 인증번호 요청, 인증번호 확인 요청, 인증번호 세션종료 요청, 비밀번호 초기화 요청
        // 그 외  : redirect auth/login + alert(로그인 해주세요.)

        $loginPossibleUrl = [
            'auth/login',
            'user/code/send',
            'user/code/check',
            'user/code/sessionout',
            'user/reset/password',
            'test/session'
        ];

        // 4. 로그인은 했지만 처음 생성된 유저일 때 : $_SESSION['userIdx']는 있는데, $_SESSION['userInit']이 0일 때
        // 가능 : 마이페이지 화면 요청, 로그아웃 요청, 로그인 세션종료요청, 인증번호 요청, 인증번호 확인 요청, 인증번호 세션종료 요청, 이메일 변경요청, 비밀번호 변경요청
        // 그 외 : redirect auth/my-page + alert(비밀번호 변경을 우선 해주세요.)

        $firstUserPossibleUrl = [
            'user/my-page',
            'auth/logout',
            'auth/sessionout',
            'user/code/send',
            'user/code/check',
            'user/code/sessionout',
            'user/update/email',
            'user/update/password',
            'test/session'
        ];

        if (empty($url) || $url == '/') {
            $this->redirect('/mk-board/auth/login', '');
        } else if ($url == 'auth/login' && isset($_SESSION['userIdx'])) {
            $this->redirect('/mk-board/post/list', '');
        } else if(!isset($_SESSION['userIdx'])) {
            if(!in_array($url, $loginPossibleUrl)) {
                $this->redirect('/mk-board/auth/login', '로그인이 필요합니다.');
                return;
            }
        } else {
            if($_SESSION['userInit'] === 0) {
                if(!in_array($url, $firstUserPossibleUrl)) {
                    $this->redirect('/mk-board/user/my-page', '비밀번호 변경을 우선 해주세요.');
                    return;
                }
            }
        }

    }

}