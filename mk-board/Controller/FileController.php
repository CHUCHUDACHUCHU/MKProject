<?php
namespace Controller;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

use Model\File;
use Model\User;
/**
 * FileController
 * Route에서 컨트롤러 사용.
 * 파일 업로드 관련 액션
 */

class FileController extends BaseController
{
    private false|array $config;
    private LocalFilesystemAdapter $adapter;
    private Filesystem $filesystem;
    private File $file;
    private User $user;

    public function __construct()
    {
        $this->config = parse_ini_file(__DIR__ . '/../config.ini');
        $this->adapter = new LocalFilesystemAdapter($this->config['FILE_UPLOAD_PATH']);
        $this->filesystem = new Filesystem($this->adapter);  // 수정된 부분
        $this->file = new File();
        $this->user = new User();
    }

    public function create() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $fileName = $requestData['fileName'];
        $fileOriginName = $requestData['fileOriginName'];
        $fileSize = $requestData['fileSize'];
        $userIdx = $_SESSION['userIdx'];
        $result = [
            'status' => '',
            'message' => ''
        ];

        if($this->parametersCheck($userIdx, $fileName, $fileOriginName, $fileSize)) {
            $fileIdx = $this->file->create($userIdx, $fileName, $fileOriginName, $fileSize);
            if($fileIdx) {
                $result['status'] = 'success';
                $result['message'] = '파일 DB 생성에 성공하였습니다.';
                $result['fileIdx'] = $fileIdx;
            } else {
                $result['status'] = 'fail';
                $result['message'] = '파일 DB 생성에 실패하였습니다.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = '입력되지 않은 값이 있습니다.';
        }
        $this->echoJson(['result' => $result]);
    }

    public function fileUpload()
    {
        if(!empty($_FILES['file'])) {
            $fileOriginName = $_FILES['file']['name'];
            $fileSize = $this->formatSizeUnits($_FILES['file']['size']);
            $fileName = uniqid('uploaded_file_') . '.' . pathinfo($fileOriginName, PATHINFO_EXTENSION);

            $stream = fopen($_FILES['file']['tmp_name'], 'r+');
            $this->filesystem->writeStream($fileName, $stream);
            fclose($stream);

            if ($this->filesystem->fileExists($fileName)) {
                $result = ['fileName' => $fileName , 'fileOriginName' => $fileOriginName, 'fileSize' => $fileSize];
            } else {
                $result = ['error' => 'Failed to save the file.'];
            }
            $this->echoJson(['result'=>$result]);
        } else {
            $this->echoJson(['result' => 'file not come!']);
        }
    }

    public function connectFileWithPost() {
        /* body 값 */
        $requestData = json_decode(file_get_contents("php://input"), true);
        $uploadedFileIndexes = array_map('intval', $requestData['uploadedFileIndexes']);
        $postIdx = $requestData['postIdx'];

        $result = [
            'status' => '',
            'message' => ''
        ];
        $nowUser = $this->user->getUserById($_SESSION['userIdx']);

        $completes = count($uploadedFileIndexes);
        foreach ($uploadedFileIndexes as $fileIdx) {
            if ($this->file->connectFileWithPost($postIdx, $fileIdx)) {
                $details = "(fileIdx : " . $fileIdx . ") is connected on (postIdx : " . $postIdx . ")";
                //로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $fileIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__,
                                        details: $details);

                $completes--;
            }
        }
        if($completes === 0) {
            $result['status'] = 'success';
            $result['message'] = '게시글과 파일 연결에 성공하였습니다.';
            $result['uploadedFileIndexes'] = count($uploadedFileIndexes);
        } else {
            $result['status'] = 'fail';
            $result['message'] = '게시글과 파일 연결에 실패하였습니다.';
        }

        $this->echoJson(['result' => $result]);
    }

    /**
     * 파일 다운로드
     */
    public function download() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $fileIdx = $requestData['fileIdx'];
        $targetFile = $this->file->getFileById($fileIdx);
        $targetFileName = $targetFile['fileName'];
        $targetFileOriginName = $targetFile['fileOriginName'];
        $targetFilePostIdx = $targetFile['postIdx'];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);
        $details = "(fileIdx : " . $fileIdx . ") is connected on (postIdx : " . $targetFilePostIdx . ")";

        if($nowUser['userIdx'] === $targetFile['userIdx'] || $nowUser['userStatus'] === '관리자') {
            if ($this->filesystem->fileExists($targetFileName)) {
                // 로깅
                $this->assembleLogData(
                    userIdx: $nowUser['userIdx'],
                    userName: $nowUser['userName'],
                    targetIdx: $fileIdx,
                    targetClass: get_class($this),
                    actionFunc: __METHOD__,
                    details: $details
                );

                // 파일 헤더 설정
                header('Content-Type: application/octet-stream; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . rawurlencode($targetFileOriginName) . '"');
                header('Content-Length: ' . $this->filesystem->fileSize($targetFileName));
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

                echo $this->filesystem->read($targetFileName);
                exit();
            }
        }
    }

    /**
     * 기존 파일 삭제 기능을 담당
     * @return void
     */
    public function delete() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $fileIdx = $requestData['fileIdx'];

        $result = [
            'status' => '',
            'message' => ''
        ];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);

        if($this->parametersCheck($fileIdx)) {
                if($this->file->delete($fileIdx)) {
                    //로깅
                    $this->assembleLogData( userIdx: $nowUser['userIdx'],
                        userName: $nowUser['userName'],
                        targetIdx: $fileIdx,
                        targetClass: get_class($this),
                        actionFunc: __METHOD__);

                    $result['status'] = 'success';
                    $result['message'] = '파일이 삭제되었습니다.';
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
