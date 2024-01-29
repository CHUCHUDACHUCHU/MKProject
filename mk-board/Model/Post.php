<?php

namespace Model;

use PDO;
use PDOException;

class Post extends BaseModel {
    public function __construct()
    {
        parent::__construct();
    }

    public function getManagePosts(string $search, int $start, int $perPage, string $postStatus = null, string $departmentName=null): array
    {
        try {
            $query = "SELECT
                    p.*,
                    u.userName,
                    u.userEmail,
                    u.userStatus,
                    d.departmentName,
                    (SELECT COUNT(*) FROM comments c WHERE c.postIdx = p.postIdx AND c.deleted_at IS NULL) AS comment_count
                FROM
                    posts p
                    JOIN users u ON p.userIdx = u.userIdx
                    JOIN departments d ON u.departmentIdx = d.departmentIdx
                WHERE
                    p.title LIKE :search
                    AND u.userStatus != '관리자'
                    AND p.deleted_at IS NULL";

            // $postStatus 값이 주어진 경우에만 추가적인 필터링
            if (!empty($postStatus)) {
                $query .= " AND p.postStatus = :postStatus";
            }
            if (!empty($departmentName)) {
                $query .= " AND d.departmentName = :departmentName";
            }

            $query .= " ORDER BY p.postIdx DESC LIMIT :start, :perPage;";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->bindParam('start', $start, PDO::PARAM_INT);
            $stmt->bindParam('perPage', $perPage, PDO::PARAM_INT);

            // $postStatus 값이 주어진 경우에만 바인딩 추가
            if (!empty($postStatus)) {
                $stmt->bindValue('postStatus', $postStatus);
            }
            if (!empty($departmentName)) {
                $stmt->bindValue('departmentName', $departmentName);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Post 목록의 개수
     * @param string $search
     * @param string $postStatus
     * @param string $departmentName
     * @return int|mixed
     */
    public function countManagePosts(string $search, string $postStatus, string $departmentName): int
    {
        try {
            $query = "SELECT count(p.postIdx) 
                        FROM posts p 
                        JOIN users u ON p.userIdx = u.userIdx
                        JOIN departments d ON u.departmentIdx = d.departmentIdx
                        WHERE title like :search 
                          and u.userStatus != '관리자'
                          and p.deleted_at is null";

            // $postStatus 값이 주어진 경우에만 추가적인 필터링
            if (!empty($postStatus)) {
                $query .= " AND p.postStatus = :postStatus";
            }
            if (!empty($departmentName)) {
                $query .= " AND d.departmentName = :departmentName";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');

            // $postStatus 값이 주어진 경우에만 바인딩 추가
            if (!empty($postStatus)) {
                $stmt->bindValue('postStatus', $postStatus);
            }
            if (!empty($departmentName)) {
                $stmt->bindValue('departmentName', $departmentName);
            }


            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return 0;
        }
    }


    public function getMainPostsByAdmin(string $search, int $start, int $perPage, string $departmentName=null): array
    {
        try {
            $query = "select	
                                p.*,
                                u.userName,
                                u.userEmail,
                                u.userStatus,
                                d.departmentName,
		                        (select count(*) from comments c where c.postIdx = p.postIdx and c.deleted_at is null) as comment_count
                                from posts p
                                join users u on p.userIdx = u.userIdx
                                join departments d on u.departmentIdx = d.departmentIdx
                                where p.title like :search
                                  and p.postStatus = '승인'
                                  and p.deleted_at is null";

            if (!empty($departmentName)) {
                $query .= " AND d.departmentName = :departmentName";
            }

            $query .= " order by p.postIdx desc limit :start, :perPage;";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->bindParam('start', $start, PDO::PARAM_INT);
            $stmt->bindParam('perPage', $perPage, PDO::PARAM_INT);

            // $postStatus 값이 주어진 경우에만 바인딩 추가
            if (!empty($departmentName)) {
                $stmt->bindValue('departmentName', $departmentName);
            }

            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Post 목록의 개수(어드민 메인페이지)
     * @param string $search
     * @param string $departmentName
     * @return int|mixed
     */
    public function countMainPostsByAdmin(string $search, string $departmentName): int
    {
        try {
            $query = "SELECT count(p.postIdx) 
                        FROM posts p 
                        JOIN departments d ON u.departmentIdx = d.departmentIdx
                        WHERE title like :search 
                          and p.postStatus = '승인'
                          and p.deleted_at is null";

            // $postStatus 값이 주어진 경우에만 추가적인 필터링
            if (!empty($departmentName)) {
                $query .= " AND d.departmentName = :departmentName";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');

            // $postStatus 값이 주어진 경우에만 바인딩 추가
            if (!empty($departmentName)) {
                $stmt->bindValue('departmentName', $departmentName);
            }

            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return 0;
        }
    }


    public function getMainPostsByCommon(string $search, int $userIdx, int $start, int $perPage): array
    {
        try {
            $query = "select	
                                p.*,
                                u.userName,
                                u.userEmail,
                                u.userStatus,
                                d.departmentName,
		                        (select count(*) from comments c where c.postIdx = p.postIdx and c.deleted_at is null) as comment_count
                                from posts p
                                join users u on p.userIdx = u.userIdx
                                join departments d on u.departmentIdx = d.departmentIdx
                                where 
                                      p.title like :search and 
                                      u.userIdx =:userIdx and
                                      p.postStatus = '승인' and 
                                      p.deleted_at is null
                                order by p.postIdx desc limit :start, :perPage;";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->bindParam('start', $start, PDO::PARAM_INT);
            $stmt->bindParam('perPage', $perPage, PDO::PARAM_INT);
            $stmt->bindParam('userIdx', $userIdx, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Post 목록의 개수(일반 메인페이지)
     * @param string $search
     * @param int $userIdx
     * @return int|mixed
     */
    public function countMainPostsByCommon(string $search, int $userIdx): int
    {
        try {
            $query = "SELECT count(p.postIdx) 
                        FROM posts p
                        JOIN users u ON u.userIdx = p.userIdx
                        WHERE title like :search 
                          and u.userIdx = :userIdx
                          and p.postStatus = '승인'
                          and p.deleted_at is null";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->bindParam('userIdx', $userIdx, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return 0;
        }
    }




    public function getMyPostsByMyPage(int $userIdx, string $search, int $start, int $perPage): array
    {
        try {
            $query = "select	
                                p.*,
                                u.*,
		                        (select count(*) from comments c where c.postIdx = p.postIdx) as comment_count,
                                case when timestampdiff(minute, p.created_at, now()) <= 1440 then 1
                                else 0 end as is_new
                                from posts p
                                join users u on p.userIdx = u.userIdx
                                where u.userIdx = :userIdx and p.title like :search and p.deleted_at is null
                                order by
                                     case when p.postStatus = '공지' then 0 else 1 end , p.postIdx desc 
                                limit :start, :perPage;";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam('userIdx', $userIdx, PDO::PARAM_INT);
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
     * Post 자신의 목록의 개수
     * @param int $userIdx
     * @param string $search
     * @return int|mixed
     */
    public function countMine(int $userIdx, string $search): int
    {
        try {
            $query = "SELECT count(p.postIdx) FROM posts p WHERE title like :search and userIdx =:userIdx and p.deleted_at is null";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->bindValue('userIdx', $userIdx);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return 0;
        }
    }


    public function getMyAllPostsForDelete(int $userIdx): array
    {
        try {
            $query = "select p.*,
                             u.*
                        from posts p
                        join users u on p.userIdx = u.userIdx
                        where u.userIdx =:userIdx and p.deleted_at is null
                      ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'userIdx' => $userIdx,
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }


    /**
     * Post에서 공지 데이터 count 가져오기
     * @return int|mixed
     */
    public function getNotifyAll(): array
    {
        try {
            $query = "select p.*,
                             u.*,
                             d.departmentName,
                             (select count(*) from comments c where c.postIdx = p.postIdx) as comment_count
                        from posts p
                        join users u on p.userIdx = u.userIdx
                        join departments d on u.departmentIdx = d.departmentIdx
                        where p.postStatus = '공지' and p.deleted_at is null
                      ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }



    /**
     * Post에서 공지 데이터 count 가져오기
     * @return int|mixed
     */
    public function countNotifyAll(): int
    {
        try {
            $query = "SELECT count(p.postIdx) FROM posts p WHERE p.postStatus = '공지' and p.deleted_at is null";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return 0;
        }
    }



    /**
     * Post 데이터 가져오기
     * @param int $postIdx
     * @return array|mixed
     */
    public function getPostById(int $postIdx)
    {
        try {
            $query = "select
                                p.*,
                                u.*
                                from posts p
                                join users u on p.userIdx = u.userIdx
                                where postIdx = :postIdx and p.deleted_at is null
                                LIMIT 1";
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
     * @param int $userIdx
     * @param string $title
     * @param string $content
     * @param string $commonOrNotifyRadio
     * @return array|mixed
     */
    public function create(int $userIdx, string $title, string $content, string $commonOrNotifyRadio)
    {
        try {
            $query = "INSERT INTO posts (userIdx, title, content, postStatus) VALUES (:userIdx, :title, :content, :postStatus)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'userIdx' => $userIdx,
                'title' => $title,
                'content' => $content,
                'postStatus' => $commonOrNotifyRadio
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Post 수정
     * @param int $postIdx
     * @param string $title
     * @param string $content
     * @return bool
     */
    public function update(int $postIdx, string $title, string $content): bool
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
     * 게시글 데이터 수정하기
     * 게시글 권한 수정
     * @param $postStatus
     * @param $postIdx
     * @return array|mixed
     */
    public function updateStatus($postStatus, $postIdx)
    {
        try {
            $query = "update posts set postStatus =:postStatus where postIdx =:postIdx ";
            return $this->conn->prepare($query)->execute([
                'postStatus' => $postStatus,
                'postIdx' => $postIdx
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }



    /**
     * Post 삭제 (논리적!)
     * @param int $postIdx
     * @return bool
     */
    public function delete(int $postIdx): bool {
        try {
            $query = "update posts set deleted_at = NOW() where postIdx =:postIdx";
            return $this->conn->prepare($query)->execute([
                'postIdx' => $postIdx
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 삭제된 User에 해당하는 post 논리적 삭제
     * @param $userIdx
     * @return bool
     */
    public function deletePostsByUser($userIdx): bool
    {
        try {
            $query = "UPDATE posts SET deleted_at = NOW() WHERE userIdx = :userIdx";
            return $this->conn->prepare($query)->execute([
                'userIdx' => $userIdx,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * Post 조회 수 증가
     * @param int $postIdx
     * @return bool|void
     */
    public function increaseViews(int $postIdx): bool
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