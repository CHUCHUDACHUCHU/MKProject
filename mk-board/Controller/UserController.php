<?php
namespace Controller;
use Model\User;
use Model\Post;
use Model\Comment;
use Model\File;

class UserController extends BaseController {
    private $user;
    private $post;
    private $comment;
    private $file;

    public function __construct() {
        $this->user = new User();
        $this->post = new Post();
        $this->comment = new Comment();
        $this->file = new File();
    }

    public function userCodeStatus() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $userEmail = $requestData['userEmail'];
        $result = [
            'status' => '',
            'message' => ''
        ];

        if(!isset($_SESSION['userInit'])) {
            if(!$this->user->getUserByEmail($userEmail)) {
                $result['status'] = 'userNotFound';
                $result['message'] = '해당 이메일로 가입된 사용자가 존재하지 않습니다.';
                $this->echoJson(['result' => $result]);
                return;
            }
        }
        if(isset($_SESSION['userInit'])) {
            if($this->user->getUserByEmail($userEmail)) {
                $result['status'] = 'userAlreadyExist';
                $result['message'] = '해당 이메일로 가입된 사용자가 이미 존재합니다.';
                $this->echoJson(['result' => $result]);
                return;
            }
        }
        if (isset($_SESSION['verification_code']) && isset($_SESSION['verification_time']) && (time() - $_SESSION['verification_time']) <= 180) {
            $result['status'] = 'codeExist';
            $result['message'] = '인증번호가 이미 전송되었습니다. 3분 후 다시 시도해주세요.';
            $this->echoJson(['result' => $result]);
            return;
        }
        $result['status'] = 'success';
        $result['message'] = '사용자 필터링 완료';

        $this->echoJson(['result' => $result]);
    }

    public function create() {
        //HTML 폼으로 부터 왔기 때문에 이렇게 받을 수 있음

        $userName = $_POST['userName'];
        $userEmail = $_POST['userEmail'];
        $departmentIdx = $_POST['departmentIdx'];
        $userPhone = $_POST['userPhone'];
        $userStatus = $_POST['userStatus'];

        $salt = '$5$QOPrAVIK$';
        $userPw = crypt('MKboard1234', $salt);

        if($this->user->getUserByEmail($userEmail)) {
            $this->redirectBack('가입된 이메일이 존재합니다.');
        }else {
            if($this->parametersCheck($userName, $userEmail, $userPw, $departmentIdx, $userPhone, $userStatus)) {
                if($this->user->create($userName, $userEmail, $userPw, $departmentIdx, $userPhone, $userStatus)) {
                    $mailSubject = "MKBoard 사용자 생성이 완료되었습니다. $userName 님";
                    $mailBody = "
                                <b>사용자 이메일: $userEmail</b><br/>
                                <b>기본 비밀번호: MKboard1234</b><br/>
                                <br/>
                                <br/>
                                <b>기본 비밀번호를 변경해야 이용이 가능합니다.</b><br/>
                                도메인 주소 : http://localhost/mk-board<br/>
                                본 메일은 MK-Board에서 발송된 것입니다.
                            ";
                    $result =$this->sendEmail($userEmail, $mailSubject, $mailBody);
                    if($result === 'fail') {
                        $this->redirectBack('회원생성 이메일이 보내지지 않았습니다. 이메일을 수동으로 보내주세요.');
                    }
                    $this->redirect('mk-board/user/manage', '회원이 생성되었습니다.');
                } else {
                    $this->redirectBack('DB 생성에 실패했습니다.');
                }
            } else {
                $this->redirectBack('입력되지 않은 값이 있습니다.');
            }
        }
    }


    public function updateAll() {
        $userName = $_POST['userName'];
        $departmentIdx = $_POST['departmentIdx'];
        $userPhone = $_POST['userPhone'];
        $userIdx = $_SESSION['userIdx'];

        if($this->parametersCheck($userName, $departmentIdx, $userPhone)) {
            $userNamePattern = '/^[A-Za-z가-힣]{1,10}$/';
            $userPhonePattern = '/^010-\d{4}-\d{4}$/';

            if(preg_match($userNamePattern, $userName) && preg_match($userPhonePattern, $userPhone)) {
                if($this->user->updateAll($userName, $departmentIdx, $userPhone, $userIdx)) {
                    $this->redirect('/mk-board/user/my-page', '사용자 정보가 수정되었습니다.');
                } else {
                    $this->redirectBack('DB 변경에 실패하였습니다.');
                }
            } else {
                $this->redirectBack('입력값이 유효하지 않습니다. 다시 확인해 주세요.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }


    /**
     * 인증번호 이메일발송
     * @important 이 메소드는 json 리턴입니다.
     */
    public function codeSend()
    {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $userEmail = $requestData['email'];
        $result = [
            'status' => '',
            'message' => ''
        ];

        // 새로운 인증번호 생성
        $verificationCode = rand(100000, 999999);
        // 이메일 발송
        $mailSubject = "[MKBoard] 이메일 인증번호입니다.";
        $mailBody = "
                     <b>인증번호: $verificationCode</b>
                     <br/>
                     <br/>
                     본 메일은 MK-Board에서 발송된 것입니다.
                    ";
        $result['status'] = $this->sendEmail($userEmail, $mailSubject, $mailBody);
        if($result['status'] === 'success') {
            $result['message'] = '인증번호 전송에 성공하였습니다.';

            // 세션에 새로운 인증번호와 현재 시간 저장
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['verification_time'] = time();
        } else {
            $result['message'] = '인증번호 전송에 실패하였습니다 : PHPMailer 오류';
        }

        $this->echoJson(['result' => $result]);
    }

    /**
     * 인증번호 확인 요청
     * @important 이 메소드는 json 리턴입니다.
     */
    public function codeCheck() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $code = $requestData['code'];
        $result = [
            'status' => '',
            'message' => ''
        ];
        if(!isset($_SESSION['verification_code'])) {
            $result['status'] = 'expire';
            $result['message'] = '인증번호 세션이 만료되었습니다.';
        }else {
            if($_SESSION['verification_code'] == $code) {
                $result['status'] = 'success';
                $result['message'] = '확인되었습니다.';
            } else {
                $result['status'] = 'fail';
                $result['message'] = '인증번호가 일치하지 않습니다.';
            }
        }
        $this->echoJson(['result' => $result]);
    }

    /**
     * 인증번호 세션 만료 요청
     */
    public function sessionout() {
        unset($_SESSION['verification_code']);
    }


    /**
     * 이메일 수정 요청
     */
    public function updateEmail() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $changeEmail = $requestData['changeEmail'];
        $userIdx = $_SESSION['userIdx'];
        $result = [
            'status' => '',
            'message' => ''
        ];


        if($this->user->getUserByEmail($changeEmail)) {
            $result['status'] = 'fail';
            $result['message'] = '해당 이메일로 이미 가입된 사용자가 존재합니다.';
        } else if($this->parametersCheck($changeEmail, $userIdx)) {
            if($this->user->updateEmail($changeEmail, $userIdx)) {
                $result['status'] = 'success';
                $result['message'] = '이메일 변경에 성공하였습니다.';
            } else {
                $result['status'] = 'fail';
                $result['message'] = 'DB 변경에 실패하였습니다.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = '입력되지 않은 값이 있습니다.';
        }
        $this->echoJson(['result' => $result]);
    }

    public function updatePassword() {
        $userIdx = $_SESSION['userIdx'];
        $userInit = $_SESSION['userInit'];
        $targetUser = $this->user->getUserById($userIdx);
        $nowPassword = $_POST['nowPassword'];
        $changePassword = $_POST['changePassword'];

        $salt = '$5$QOPrAVIK$';
        $nowPassword = crypt($nowPassword, $salt);
        $changePassword = crypt($changePassword, $salt);

        if($this->parametersCheck($nowPassword, $changePassword)) {
            if($targetUser['userPw'] === $nowPassword) {
                if($this->user->updatePassword($userIdx, $changePassword, $userInit)) {
                    $this->redirect('/mk-board/auth/logout', '비밀번호 변경 성공!, 다시 로그인해주세요!');
                } else {
                    $this->redirectBack('DB 변경에 실패하였습니다.');
                }
            } else {
                $this->redirectBack('현재 비밀번호가 일치하지 않습니다!');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }

    /**
     * 회원 권한 변경 요청
     */
    public function updateStatus() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $userStatus = $requestData['userStatus'];
        $userEmail = $requestData['userEmail'];
        $result = [
            'status' => '',
            'message' => ''
        ];


        if($this->parametersCheck($userStatus, $userEmail)) {
            if($this->user->updateStatus($userStatus, $userEmail)) {
                $result['status'] = 'success';
                $result['message'] = '회원 권한 변경에 성공하였습니다.';
            } else {
                $result['status'] = 'fail';
                $result['message'] = 'DB 변경에 실패하였습니다.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = '입력되지 않은 값이 있습니다.';
        }
        $this->echoJson(['result' => $result]);
    }

    /**
     * User 삭제하기 (논리적!)
     */
    public function delete() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $userEmail = $requestData['userEmail'];
        $userIdx = $this->user->getUserByEmail($userEmail)['userIdx'];
        $posts = $this->post->getMyAllPostsForDelete($userIdx);
        $result = [
            'status' => '',
            'message' => ''
        ];

        if ($this->parametersCheck($userEmail)) {
            if ($this->user->delete($userEmail)) {
                foreach ($posts as $item) {
                    $this->comment->deleteCommentsByPost($item['postIdx']);
                    $this->file->deleteFilesByPost($item['postIdx']);
                }
                $this->post->deletePostsByUser($userIdx);
                // 각각의 게시글에 대한 댓글 삭제
                $result['status'] = 'success';
                $result['message'] = '회원 삭제에 성공하였습니다.';
            } else {
                $result['status'] = 'fail';
                $result['message'] = 'DB 변경에 실패하였습니다.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = '입력되지 않은 값이 있습니다.';
        }
        $this->echoJson(['result' => $result]);
    }

    /**
     * 비밀번호 초기화 요청
     * @important 이 메소드는 json 리턴입니다.
     */
    public function resetPassword() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $userEmail = $requestData['userEmail'];
        $result = [
            'status' => '',
            'message' => ''
        ];

        $salt = '$5$QOPrAVIK$';
        $changePassword = crypt('MKboard1234', $salt);

        if($this->parametersCheck($userEmail, $changePassword)) {
            if($this->user->resetPassword($userEmail, $changePassword)) {
                $mailSubject = "MKBoard 사용자의 비밀번호가 초기화되었습니다.";
                $mailBody = "
                                <b>사용자 이메일: $userEmail</b><br/>
                                <b>기본 비밀번호: MKboard1234</b><br/>
                                <br/>
                                <br/>
                                <b>기본 비밀번호를 변경해야 이용이 가능합니다.</b><br/>
                                도메인 주소 : http://localhost/mk-board<br/>
                                본 메일은 MK-Board에서 발송된 것입니다.
                            ";
                if($this->sendEmail($userEmail, $mailSubject, $mailBody)) {
                    $result['status'] = 'success';
                    $result['message'] = '비밀번호 초기화에 성공하였습니다. 이메일을 확인해주세요.';
                } else {
                    $result['status'] = 'fail';
                    $result['message'] = '비밀번호 초기화 메일전송에 실패하였습니다.';
                }
            } else {
                $result['status'] = 'fail';
                $result['message'] = 'DB 변경에 실패하였습니다.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = '입력되지 않은 값이 있습니다.';
        }
        $this->echoJson(['result' => $result]);
    }

}