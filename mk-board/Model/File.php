<?php
namespace Model;

use PDO;
use PDOException;

class File extends BaseModel {
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * 하나의 파일 정보 가져오기
     * @param $fileIdx
     * @return
     */
    public function getFileById($fileIdx): array
    {
        try {
            $query = "SELECT f.*
                        FROM files f
                        WHERE fileIdx = :fileIdx
                     ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'fileIdx' => $fileIdx,
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }


    /**
     * 파일 데이터 생성하기
     * @param $fileName
     * @param $fileOriginName
     * @param $fileSize
     * @param $userIdx
     * @return array|mixed
     */
    public function create($userIdx, $fileName, $fileOriginName, $fileSize)
    {
        try {
            $query = "INSERT INTO files (userIdx, fileName, fileOriginName, fileSize) 
                                    VALUES (:userIdx, :fileName, :fileOriginName, :fileSize)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'userIdx' => $userIdx,
                'fileName' => $fileName,
                'fileOriginName' => $fileOriginName,
                'fileSize' => $fileSize
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * 파일과 게시글 연결하기
     * @param $postIdx
     * @param $fileIdx
     * @return array|mixed
     */
    public function updatePostIdx($postIdx, $fileIdx)
    {
        try {
            $query = "update files set postIdx =:postIdx  where fileIdx =:fileIdx";
            return $this->conn->prepare($query)->execute([
                'postIdx' => $postIdx,
                'fileIdx' => $fileIdx
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 해당 게시글의 파일 가져오기
     * @param $postIdx
     * @return array
     */
    public function getAllFilesByPostIdx(int $postIdx): array
    {
        try {
            $query = "select f.*
                        from files f
                        where f.postIdx =:postIdx and f.deleted_at is null
                      ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'postIdx' => $postIdx,
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * File 삭제 (논리적!)
     * @param int $fileIdx
     * @return bool
     */
    public function delete(int $fileIdx): bool {
        try {
            $query = "update files set deleted_at = NOW() where fileIdx =:fileIdx";
            return $this->conn->prepare($query)->execute([
                'fileIdx' => $fileIdx
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * 삭제된 Post에 해당하는 파일 논리적 삭제
     * @param $postIdx
     * @return bool
     */
    public function deleteFilesByPost($postIdx): bool
    {
        try {
            $query = "UPDATE files SET deleted_at = NOW() WHERE postIdx = :postIdx";
            return $this->conn->prepare($query)->execute([
                'postIdx' => $postIdx,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

}