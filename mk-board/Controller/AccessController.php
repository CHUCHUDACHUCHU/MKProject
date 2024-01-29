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
            'user/code/status',
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
            'user/code/status',
            'user/code/send',
            'user/code/check',
            'user/code/sessionout',
            'user/update/email',
            'user/update/password',
            'test/session'
        ];

        // 5. 회원의 상태가 정지일 때 : $nowUser['userStatus'] === '정지'

        $stopUserPossibleUrl = [
            'auth/logout'
        ];

        // 6. 일반 회원이 가지 못하는 url

        $commonUserImpossibleUrl = [
            'user/manage',
            'post/manage',
            'user/read',
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
            $nowUser = $this->user->getUserByIdIncludeDeleted($_SESSION['userIdx']);
            if($nowUser['userStatus'] === '정지' || $nowUser['deleted_at']) {
                if(!in_array($url, $stopUserPossibleUrl)) {
                    $this->redirect('/mk-board/auth/logout', '정지 혹은 삭제 상태입니다.\n관리자에게 문의하세요.\nEmail : chu.gyoyoon@mkinternet.com');
                    return;
                }
            }
            if($_SESSION['userInit'] === 0) {
                if(!in_array($url, $firstUserPossibleUrl)) {
                    $this->redirect('/mk-board/user/my-page', '비밀번호 변경을 우선 해주세요.');
                    return;
                }
            }
            if($nowUser['userStatus'] === '일반' && in_array($url, $commonUserImpossibleUrl)) {
                $this->redirect('/mk-board/', '접근할 수 없는 권한입니다.');
                return;
            }
        }
    }
}