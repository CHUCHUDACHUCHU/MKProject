<?php

namespace Model;

use PDO;
use PDOException;

class Post extends BaseModel {
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllPosts(string $search, int $start, int $perPage): array
    {
        try {
            $query = "select	
                                p.*,
                                u.userName,
                                u.userEmail,
		                        (select count(*) from comments c where c.postIdx = p.postIdx) as comment_count,
                                case when timestampdiff(minute, p.created_at, now()) <= 1440 then 1
                                else 0 end as is_new
                                from posts p
                                join users u on p.userIdx = u.userIdx
                                where p.title like :search
                                order by p.postIdx desc limit :start, :perPage;";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->bindParam('start', $start, PDO::PARAM_INT);
            $stmt->bindParam('perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Post 데이터 가져오기
     * @param $postIdx int Post의 postIdx
     * @return array|mixed
     */
    public function getPost(int $postIdx)
    {
        try {
            $query = "select
                                p.*,
                                u.userName
                                from posts p
                                join users u on p.userIdx = u.userIdx
                                where postIdx = :postIdx LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'postIdx' => $postIdx
            ]);
            return $stmt->fetch();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Post 추가
     * @param $userIdx
     * @param $title
     * @param $content
     * @return bool
     */
    public function create($userIdx, $title, $content): bool
    {
        try {
            $query = "INSERT INTO posts (userIdx, title,content) VALUES (:userIdx, :title, :content)";
            return $this->conn->prepare($query)->execute([
                'userIdx' => $userIdx,
                'title' => $title,
                'content' => $content
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Post 수정
     * @param $postIdx
     * @param $title
     * @param $content
     * @return bool
     */
    public function update($postIdx, $title, $content): bool
    {
        try {
            $query = "update posts set title =:title, content =:content  where postIdx =:postIdx";
            return $this->conn->prepare($query)->execute([
                'postIdx' => $postIdx,
                'title' => $title,
                'content' => $content
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * Post 목록의 개수
     * @param $search string 검색어
     * @return int|mixed
     */
    public function count(string $search)
    {
        try {
            $query = "SELECT count(postIdx) FROM posts WHERE title like :search";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    /**
     * Post 조회 수 증가
     * @param $postIdx int Post의 postIdx
     * @return bool|void
     */
    public function increaseViews($postIdx): bool
    {
        try {
            // post_views 쿠키가 없으면 조회수 증가
            if (!isset($_COOKIE['post_views' . $postIdx])) {
                $stmt = $this->conn->prepare("update posts set views = views + 1 where postIdx = :postIdx");
                $stmt->bindParam('postIdx', $postIdx);
                $stmt->execute();
                // 조회수 증가 후 하루짜리 쿠키 생성
                setcookie('post_views' . $postIdx, true, time() + 60 * 60 * 24, '/');
                return true;
            }
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

}