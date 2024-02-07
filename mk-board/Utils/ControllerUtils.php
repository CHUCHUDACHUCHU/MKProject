<?php
namespace Utils;

use JetBrains\PhpStorm\Pure;
use Model\Log;
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
    public function redirectBack(string $message)
    {
        echo "<script>
                alert('$message');
                history.back();
              </script>";
        exit();
    }

    /**
     * json 형식으로 출력 (Ajax 요청에 반환 값)
     * @param array $data array|object
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

    /**
     * 파일 용량 format
     * @param $bytes
     * @return string
     */
    #[Pure] public function formatSizeUnits($bytes): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function sendEmail($recipients, $subject, $body): string
    {
        $config = parse_ini_file(__DIR__ . '/../config.ini');
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // 디버그 모드, DEBUG_OFF 시 출력 없음
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPKeepAlive = true;
        $mail->Username = $config['SMTP_EMAIL'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['SMTP_PORT'];
        $mail->Timeout = 10;

        $mail->setFrom($config['SMTP_EMAIL'], 'MKBoard');

        // 다중 이메일 주소 추가
        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient);
        }

        // 메일 제목, 내용 세팅
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        return $mail->send() ? "success" : "fail";
    }

    public function assembleLogData(
        string $actionType = "POST",
        int $userIdx = null,
        string $userName = null,
        int $targetIdx = null,
        string $targetClass = null,
        string $actionFunc = null,
        string $updateStatus = null,
        string $details = null
    )
    {
        $log = new Log();
        $log->create($actionType, $userIdx, $userName, $targetIdx, substr($targetClass, 11), explode('::', $actionFunc)[1], $updateStatus, $details);
    }
}