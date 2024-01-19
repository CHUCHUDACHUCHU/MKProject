<?php
namespace Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

trait ControllerUtils   {
    /**
     * 경로로 이동하고 메시지를 출력
     * @param $path string 주소
     * @param $message string 메시지
     * @return void
     */
    public function redirect(string $path, string $message)
    {
        if($message === '') {
            echo "<script>
                location.href='$path';
              </script>";
            exit();
        } else {
            echo "<script>
                    alert('$message');
                    location.href='$path';
                  </script>";
            exit();
        }
    }

    /**
     * 이전 페이지로 이동하고 메시지를 출력
     * @param $message string 메시지
     * @return void
     */
    public function redirectBack($message)
    {
        echo "<script>
                alert('$message');
                history.back();
              </script>";
        exit();
    }

    /**
     * json 형식으로 출력 (Ajax 요청에 반환 값)
     * @param $data array|object
     * @return void
     */
    public function echoJson(array $data){
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * 파라미터가 존재 여부 확인
     * @param ...$parameters string 검사할 파라미터
     * @return bool
     */
    public function parametersCheck(...$parameters): bool
    {
        foreach ($parameters as $parameter){
            if (empty($parameter)){
                return false;
            }
        }
        return true;
    }

    public function sendEmail($userEmail, $subject, $body): string
    {
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // 디버그 모드, DEBUG_OFF 시 출력 없음
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yoon7548@gmail.com';
        $mail->Password = 'ellh yjlt wtct vpng';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 10;
        $keyPath = '/etc/ssl/private/my-ssl-cert.key';
        $certPath = '/etc/ssl/certs/my-ssl-cert.pem';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'local_cert' => $certPath,
                'local_pk' => $keyPath,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        );

        $mail->setFrom('yoon7548@gmail.com', 'MK-Board');
        $mail->addAddress($userEmail, $userEmail);

        // 메일 제목, 내용 세팅
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        return $mail->send() ? "success" : "fail";
    }
}