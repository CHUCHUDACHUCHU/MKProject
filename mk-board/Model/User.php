<?php
namespace Model;

use PDO;
use PDOException;

class User extends BaseModel {
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * User의 개수
     * @param string $search
     * @return int|mixed
     */
    public function countAll(string $search): int
    {
        try {
            $query = "SELECT count(u.userIdx) FROM users u WHERE userName like :search and u.deleted_at is null";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue('search', '%' . ($search ?? '') . '%');
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    public function getAllUsers(string $search, int $start, int $perPage): array
    {
        try {
            $query = "select	
                                u.*
                                from users u
                                where u.userName like :search and u.deleted_at is null
                                order by u.userIdx desc limit :start, :perPage;";

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
     * 회원 데이터 생성하기
     * @param $userName
     * @param $userEmail
     * @param $userPw
     * @param $userDepart
     * @param $userPhone
     * @param $userStatus
     * @return array|mixed
     */
    public function create($userName, $userEmail, $userPw, $userDepart, $userPhone, $userStatus)
    {
        try {
            $query = "INSERT INTO users (userName, userEmail, userPw, userDepart, userPhone, userStatus) 
                                    VALUES (:userName, :userEmail, :userPw, :userDepart, :userPhone, :userStatus)";
            return $stmt = $this->conn->prepare($query)->execute([
                'userName' => $userName,
                'userEmail' => $userEmail,
                'userPw' => $userPw,
                'userDepart' => $userDepart,
                'userPhone' => $userPhone,
                'userStatus' => $userStatus
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 회원 데이터 수정하기
     * 회원 부가 정보 수정하기
     * @param $userName
     * @param $userDepart
     * @param $userPhone
     * @param $userIdx
     * @return array|mixed
     */
    public function updateAll($userName, $userDepart, $userPhone, $userIdx)
    {
        try {
            $query = "update users set userName =:userName, userDepart =:userDepart, userPhone =:userPhone where userIdx =:userIdx ";
            return $this->conn->prepare($query)->execute([
                'userName' => $userName,
                'userDepart' => $userDepart,
                'userPhone' => $userPhone,
                'userIdx' => $userIdx
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 회원 데이터 수정하기
     * 회원 이메일 수정
     * @param $changeEmail
     * @param $userIdx
     * @return array|mixed
     */
    public function updateEmail($changeEmail, $userIdx)
    {
        try {
            $query = "update users set userEmail =:changeEmail where userIdx =:userIdx ";
            return $this->conn->prepare($query)->execute([
                'changeEmail' => $changeEmail,
                'userIdx' => $userIdx
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 회원 데이터 수정하기
     * 회원 비밀번호 수정
     * @param $userIdx
     * @param $changePassword
     * @param $userInit
     * @return array|mixed
     */
    public function updatePassword($userIdx, $changePassword, $userInit)
    {
        try {
            if($userInit === 0) {
                $query = "update users set userPw =:changePassword, userInit =1 where userIdx =:userIdx ";
            } else {
                $query = "update users set userPw =:changePassword where userIdx =:userIdx ";
            }
            return $this->conn->prepare($query)->execute([
                'userIdx' => $userIdx,
                'changePassword' => $changePassword
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 회원 데이터 수정하기
     * 회원 비밀번호 초기화
     * @param $userEmail
     * @param $changePassword
     * @return array|mixed
     */
    public function resetPassword($userEmail, $changePassword)
    {
        try {
            $query = "update users set userPw =:changePassword, userInit = 0 where userEmail =:userEmail ";
            return $this->conn->prepare($query)->execute([
                'userEmail' => $userEmail,
                'changePassword' => $changePassword
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }



    /**
     * 회원 데이터 가져오기(userEmail)
     * @param $userEmail string User의 userEmail
     * @return array|mixed
     */
    public function getUserByEmail(string $userEmail)
    {
        try {
            $query = "SELECT u.* FROM users u WHERE userEmail = :userEmail and u.deleted_at is null LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'userEmail' => $userEmail
            ]);
            return $stmt->fetch();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 회원 데이터 가져오기(userEmail)
     * @param $userIdx int User의 userEmail
     * @return array|mixed
     */
    public function getUserById(int $userIdx)
    {
        try {
            $query = "SELECT u.* FROM users u WHERE userIdx = :userIdx and u.deleted_at is null LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'userIdx' => $userIdx
            ]);
            return $stmt->fetch();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}