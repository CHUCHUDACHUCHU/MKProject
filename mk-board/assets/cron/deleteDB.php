<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Model\User;
use Model\Post;
use Model\Comment;
use Model\File;
use Model\Log;


$user = new User();
$post = new Post();
$comment = new Comment();
$file = new File();
$log = new Log();
$config = parse_ini_file(__DIR__ . '/../../config.ini');
$adapter = new LocalFilesystemAdapter($config['FILE_UPLOAD_PATH']);
$filesystem = new Filesystem($adapter);  // 수정된 부분

try {
    // 로그 파일 경로 설정
    $logFile = $config['SCHEDULER_LOG_PATH'];
    $currentTime = date('Y-m-d H:i:s');
    $logShow = '';
    
    $logMessage = "[$currentTime] ------ DB 물리적 삭제 스케쥴러 시작 ------\n";
    $logShow .= $logMessage;
    file_put_contents($logFile, $logMessage, FILE_APPEND);


    /*--------------------------------------------------------*/

    $count = $user->countPhysicalDeletedTarget();
    if($count > 0) {
        $logMessage = "[$currentTime] User 삭제 시작\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
        $user->deleteByCron();
        $logMessage = "[$currentTime] User 삭제 종료\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
    }


    /*--------------------------------------------------------*/

    $count = $post->countPhysicalDeletedTarget();
    if($count > 0) {
        $logMessage = "[$currentTime] Post 삭제 시작\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
        $post->deleteByCron();
        $logMessage = "[$currentTime] Post 삭제 종료\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
    }

    /*--------------------------------------------------------*/

    $count = $comment->countPhysicalDeletedTarget();
    if($count > 0) {
        $logMessage = "[$currentTime] Comment 삭제 시작\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
        $comment->deleteByCron();
        $logMessage = "[$currentTime] Comment 삭제 종료\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
    }

    /*--------------------------------------------------------*/

    $count = $file->countPhysicalDeletedTarget();
    if($count > 0) {
        $logMessage = "[$currentTime] File 삭제 시작\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;

        $logMessage = "[$currentTime] File 서버 삭제 시작\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
        $targetFiles = $file->getAllPhysicalDeleteTargetFile();
        foreach ($targetFiles as $targetFile) {
            $targetFileName = $targetFile['fileName'];
            if($filesystem->fileExists($targetFileName)) {
                $filesystem->delete($targetFileName);
            }
        }
        $logMessage = "[$currentTime] File 서버 삭제 종료\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
        
        $file->deleteByCron();

        $logMessage = "[$currentTime] File 삭제 종료\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        $logShow .= $logMessage;
    }

    /*--------------------------------------------------------*/

    $logMessage = "[$currentTime] ------ DB 물리적 삭제 스케쥴러 종료 ------\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    $logShow .= $logMessage;

    $log->create("Scheduler", 0, "SERVER", null, "SERVER", "physicalDeleteByServer", "delete", $logShow);

}catch (Exception $e) {
    file_put_contents($logFile, $e->getMessage(), FILE_APPEND);
}
