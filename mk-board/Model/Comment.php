<?php
namespace Model;

use PDO;
use PDOException;

class Comment extends BaseModel {
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 댓글 만들기
     * @param $postIdx
     * @param $userIdx
     * @param $content
     * @return bool|string
     */
    public function create($postIdx, $userIdx, $content): bool|string
    {
        try {
            $query = "INSERT INTO comments (postIdx, userIdx, content) VALUES (:postIdx, :userIdx, :content)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'postIdx' => $postIdx,
                'userIdx' => $userIdx,
                'content' => $content
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 댓글 내용 수정
     * @param $commentIdx
     * @param $content
     * @return bool
     */
    public function update($commentIdx, $content): bool
    {
        try {
            $query = "UPDATE comments SET content =:content WHERE commentIdx =:commentIdx";
            return $this->conn->prepare($query)->execute([
                'commentIdx' => $commentIdx,
                'content' => $content
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Comment 삭제 (논리적!)
     * @param int $commentIdx
     * @return bool
     */
    public function delete(int $commentIdx): bool {
        try {
            $query = "update comments set deleted_at = NOW() where commentIdx =:commentIdx";
            return $this->conn->prepare($query)->execute([
                'commentIdx' => $commentIdx
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 삭제된 Post에 해당하는 댓글 논리적 삭제
     * @param $postIdx
     * @return bool
     */
    public function deleteCommentsByPost($postIdx): bool
    {
        try {
            $query = "UPDATE comments SET deleted_at = NOW() WHERE postIdx = :postIdx";
            return $this->conn->prepare($query)->execute([
                'postIdx' => $postIdx,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * 댓글 목록 가져오기
     * @param $postIdx
     * @return array
     */
    public function getAllComments($postIdx): array
    {
        try {
            $query = "SELECT c.*, u.userName, u.userEmail
                        FROM comments c
                        JOIN users u ON c.userIdx = u.useridx
                        WHERE postIdx = :postIdx AND c.deleted_at is null
                        ORDER BY commentIdx DESC
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
     * 하나의 댓글 정보 가져오기
     * @param $commentIdx
     * @return
     */
    public function getCommentById($commentIdx): array
    {
        try {
            $query = "SELECT c.*
                        FROM comments c
                        WHERE commentIdx = :commentIdx
                     ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'commentIdx' => $commentIdx,
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

}