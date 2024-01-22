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
     * @return bool
     */
    public function create($postIdx, $userIdx, $content): bool
    {
        try {
            $query = "INSERT INTO comments (postIdx, userIdx, content) VALUES (:postIdx, :userIdx, :content)";
            return $this->conn->prepare($query)->execute([
                'postIdx' => $postIdx,
                'userIdx' => $userIdx,
                'content' => $content
            ]);
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
                        WHERE postIdx = :postIdx AND parentIdx = 0 AND c.deleted_at is null
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
                        WHERE commentIdx = :commentIdx AND parentIdx = 0
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