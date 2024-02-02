<?php
namespace Controller;

use Model\Post;
use Model\User;

/**
 * EmailController
 * 이메일 전송 관련 액션
 */

class EmailController extends BaseController{

    /**
     * @var User
     */
    private User $user;
    private Post $post;

    public function __construct() {
        $this->user = new User();
        $this->post = new Post();
    }

    public function sendEmailController() {
        /* body 값 */
        $requestData = json_decode(file_get_contents("php://input"), true);
        $data = $requestData['data'];
        $type = $requestData['type'];
        $nowUser = $this->user->getUserById($_SESSION['userIdx']);

        if($type === 'sendEmailToAllAdminByPostCreate') {
            $targetPostIdx = $data;
            $targetPost = $this->post->getPostById($targetPostIdx);
            $adminUsers = $this->user->getAllAdminUsers();
            $nowUserName = $nowUser['userName'];
            $title = $targetPost['title'];
            $recipients = [];

            foreach ($adminUsers as $admin) {
                array_push($recipients, $admin['userEmail']);
            }

            $mailSubject = "[MKBoard] 게시글이 권한 요청이 왔습니다. 권한을 부여해주세요!";
            $mailBody = "
                            <b>$nowUserName</b>님의 게시글 권한 요청이 왔습니다.<br/>
                            <br/>
                            <br/>
                            작성자 : <b>$nowUserName<b/><br/>
                            제목 : <b>$title<b/><br/>
                            게시글 번호 : <b>$targetPostIdx<b/><br/>
                            <br/>
                            <b>해당 게시글의 권한 상태를 변경해주세요.</b><br/>
                            <br/>
                            도메인 주소 : http://localhost/mk-board<br/>
                            본 메일은 MK-Board에서 발송된 것입니다.
                        ";
            $this->sendEmail($recipients, $mailSubject, $mailBody);
        }

        else if($type === 'sendEmailToUserStatusChanged') {
            $targetUserEmail = $data;
            $targetUser = $this->user->getUserByEmail($targetUserEmail);
            $targetUserName = $targetUser['userName'];
            $targetUserStatus = $targetUser['userStatus'];
            $nowUserName = $nowUser['userName'];
            $recipients = [];
            array_push($recipients, $targetUser['userEmail']);

            $mailSubject = "[MKBoard] 회원님. 권한이 변경되었습니다!";
            $mailBody = "
                            <b>$targetUserName</b>님의 권한이 <b>$targetUserStatus<b/>으로 변경되었습니다.<br/>
                            <br/>
                            <br/>
                            권한 변경자 : <b>$nowUserName<b/><br/>
                            <br/>
                            <br/>
                            도메인 주소 : http://localhost/mk-board<br/>
                            본 메일은 MK-Board에서 발송된 것입니다.
                        ";
            $this->sendEmail($recipients, $mailSubject, $mailBody);
        }

        else if($type === 'sendEmailToPostStatusChanged') {
            $targetPostIdx = $data;
            $targetPost = $this->post->getPostById($targetPostIdx);
            $targetPostUser = $this->user->getUserById($targetPost['userIdx']);
            $targetPostUserEmail = $targetPostUser['userEmail'];
            $targetPostTitle = $targetPost['title'];
            $targetPostStatus = $targetPost['postStatus'];
            $nowUserName = $nowUser['userName'];

            $recipients = [];
            array_push($recipients, $targetPostUserEmail);

            $mailSubject = "[MKBoard] 회원님의 게시글 권한이 변경되었습니다!";
            $mailBody = "
                            회원님의 게시글 권한이 <b>$targetPostStatus<b/>으로 변경되었습니다.<br/>
                            <br/>
                            <br/>
                            게시글 번호 : <b>$targetPostIdx<b/><br/>
                            게시글 제목 : <b>$targetPostTitle<b/><br/>
                            권한 변경자 : <b>$nowUserName<b/><br/>
                            <br/>
                            <br/>
                            도메인 주소 : http://localhost/mk-board/post/read?postIdx=$targetPostIdx<br/>
                            본 메일은 MK-Board에서 발송된 것입니다.
                        ";
            $this->sendEmail($recipients, $mailSubject, $mailBody);

        }
    }
}