<?php
namespace Controller;
use Model\User;

class UserController extends BaseController {
    private $user;

    public function __construct() {
        $this->user = new user();
    }
    public function getUserByEmail() {
        $userEmail = $_GET['userEmail'];
        if($this->user->getUserByEmail($userEmail)) {
            return $this->user->getUserByEmail($userEmail);
        } else {
            $this->redirectBack('회원이 조회에 실패했습니다.');
        }
    }

    public function checkCert() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $code = $requestData['code'];
        $result = null;
        if(!isset($_SESSION['verification_code'])) {
            $result = 'expire';
        }else {
            if($_SESSION['verification_code'] == $code) {
                $result = 'success';
            } else {
                $result = 'wrong';
            }
        }
        $this->echoJson(['result' => $result]);
    }

    public function sendCert() {
        $result = null;
        if(isset($_SESSION['verification_code'])) {
            $result = 'busy';
        } else {
            // 랜덤한 6자리 인증번호 생성
            $verificationCode = rand(100000, 999999);

            //fetch를 통해서 post로 왔기 때문에 이렇게 받아와야함
            $requestData = json_decode(file_get_contents("php://input"), true);
            $changeEmail = $requestData['changeEmail'];
            $userName = $requestData['userName'];
            $mailSubject = "[MKBoard] $userName 님 이메일 인증번호입니다.";
            $mailBody = "
                     <b>인증번호: $verificationCode</b>
                     <br/>
                     <br/>
                     본 메일은 MK-Board에서 발송된 것입니다.
                    ";
            $result =$this->sendEmail($changeEmail, $userName, $mailSubject, $mailBody);
            $_SESSION['verification_code'] = $verificationCode;
        }
        $this->echoJson(['result' => $result]);
    }

    public function sessionout() {
        $authCertCheck = $_GET['authCertCheck'];
        unset($_SESSION['verification_code']);
        if($authCertCheck === 0) {
            $this->redirectBack('3분이 초과되었습니다. 다시 시도해주세요.');
        }
        $this->echoJson(['result' => "success"]);
    }

    public function create() {
        //HTML 폼으로 부터 왔기 때문에 이렇게 받을 수 있음

        $userName = $_POST['userName'];
        $userEmail = $_POST['userEmail'];
        $userDepart = $_POST['userDepart'];
        $userPhone = $_POST['userPhone'];

        $salt = '$5$QOPrAVIK$';
        $userPw = crypt('MKboard1234', $salt);

        if($this->user->getUserByEmail($userEmail)) {
            $this->redirectBack('가입된 이메일이 존재합니다.');
        }else {
            if($this->parametersCheck($userName, $userEmail, $userPw, $userDepart, $userPhone)) {
                if($this->user->create($userName, $userEmail, $userPw, $userDepart, $userPhone)) {
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
                    $result =$this->sendEmail($userEmail, $userName, $mailSubject, $mailBody);
                    if($result === 'fail') {
                        $this->redirectBack('회원이 생성 이메일이 보내지지 않았습니다. 이메일을 수동으로 보내주세요.');
                    }
                    $this->redirect('mk-board/user/manage', '회원이 생성되었습니다.');
                } else {
                    $this->redirectBack('회원이 생성에 실패했습니다.');
                }
            } else {
                $this->redirectBack('입력되지 않은 값이 있습니다.');
            }
        }
    }

    public function updateEmail() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $changeEmail = $requestData['changeEmail'];
        $userIdx = $_SESSION['userIdx'];
        $result = '';

        if($this->parametersCheck($changeEmail, $userIdx)) {
            if($this->user->updateEmail($changeEmail, $userIdx)) {
                $result = 'success';
            } else {
                $result = 'fail';
            }
        } else {
            $result = 'validation';
        }
        $this->echoJson(['result' => $result]);
    }
}