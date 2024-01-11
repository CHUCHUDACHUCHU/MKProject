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
     * 회원 데이터 생성하기
     * @param $userName
     * @param $userEmail
     * @param $userPw
     * @param $userDepart
     * @param $userPhone
     * @return array|mixed
     */
    public function create($userName, $userEmail, $userPw, $userDepart, $userPhone)
    {
        try {
            $query = "INSERT INTO users (userName, userEmail, userPw, userDepart, userPhone) 
                                    VALUES (:userName, :userEmail, :userPw, :userDepart, :userPhone)";
            return $stmt = $this->conn->prepare($query)->execute([
                'userName' => $userName,
                'userEmail' => $userEmail,
                'userPw' => $userPw,
                'userDepart' => $userDepart,
                'userPhone' => $userPhone
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
            $query = "SELECT * FROM users WHERE userEmail = :userEmail LIMIT 1";
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
     * @param $userIdx string User의 userEmail
     * @return array|mixed
     */
    public function getUserById(int $userIdx)
    {
        try {
            $query = "SELECT u.userName, u.userEmail, u.userDepart, u.userPhone 
                                        FROM users u WHERE userIdx = :userIdx LIMIT 1";
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