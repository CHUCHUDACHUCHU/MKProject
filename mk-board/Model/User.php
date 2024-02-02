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
     * @return int
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
                                u.*,
                                d.departmentName
                                from users u
                                join departments d on u.departmentIdx = d.departmentIdx
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
     * @param $departmentIdx
     * @param $userPhone
     * @param $userStatus
     * @return bool|string
     */
    public function create($userName, $userEmail, $userPw, $departmentIdx, $userPhone, $userStatus): bool|string
    {
        try {
            $query = "INSERT INTO users (userName, userEmail, userPw, departmentIdx, userPhone, userStatus) 
                                    VALUES (:userName, :userEmail, :userPw, :departmentIdx, :userPhone, :userStatus)";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'userName' => $userName,
                'userEmail' => $userEmail,
                'userPw' => $userPw,
                'departmentIdx' => $departmentIdx,
                'userPhone' => $userPhone,
                'userStatus' => $userStatus
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 회원 데이터 수정하기
     * 회원 부가 정보 수정하기
     * @param $userName
     * @param $departmentIdx
     * @param $userPhone
     * @param $userIdx
     * @return bool
     */
    public function updateMyInfo($userName, $departmentIdx, $userPhone, $userIdx): bool
    {
        try {
            $query = "update users set userName =:userName, departmentIdx =:departmentIdx, userPhone =:userPhone where userIdx =:userIdx ";
            return $this->conn->prepare($query)->execute([
                'userName' => $userName,
                'departmentIdx' => $departmentIdx,
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
     * @return bool
     */
    public function updateEmail($changeEmail, $userIdx): bool
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
     * 회원 권한 수정
     * @param $userStatus
     * @param $userEmail
     * @return bool
     */
    public function updateStatus($userStatus, $userEmail): bool
    {
        try {
            $query = "update users set userStatus =:userStatus where userEmail =:userEmail ";
            return $this->conn->prepare($query)->execute([
                'userStatus' => $userStatus,
                'userEmail' => $userEmail
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
     * @return bool
     */
    public function updatePassword($userIdx, $changePassword, $userInit): bool
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
     * @return mixed
     */
    public function resetPassword($userEmail, $changePassword): mixed
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
     * @return mixed
     */
    public function getUserByEmail(string $userEmail): mixed
    {
        try {
            $query = "SELECT 
                                u.*,
                                d.departmentName
                                FROM users u 
                                JOIN departments d on d.departmentIdx = u.departmentIdx
                                WHERE userEmail = :userEmail and u.deleted_at is null LIMIT 1";
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
     * 회원 데이터 가져오기(userIdx)
     * @param $userIdx int User의 userIdx
     * @return mixed
     */
    public function getUserById(int $userIdx): mixed
    {
        try {
            $query = "SELECT 
                                u.*,
                                d.departmentName
                                FROM users u
                                JOIN departments d on d.departmentIdx = u.departmentIdx
                                WHERE userIdx = :userIdx and u.deleted_at is null LIMIT 1";
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

    /**
     * 모든 관리자 가져오기
     * @return bool|array
     */
    public function getAllAdminUsers(): bool|array
    {
        try {
            $query = "SELECT 
                                u.*,
                                d.departmentName
                                FROM users u
                                JOIN departments d on d.departmentIdx = u.departmentIdx
                                WHERE userStatus = :userStatus and u.deleted_at is null";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'userStatus' => "관리자"
            ]);
            return $stmt->fetchAll();
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 회원 데이터 가져오기
     * 삭제된 회원이라도 가져오기!!!
     * 이유 : 이용 중 삭제를 당한 경우 확인하기 위해!
     * @param $userIdx int User의 userIdx
     * @return array|mixed
     */
    public function getUserByIdIncludeDeleted(int $userIdx)
    {
        try {
            $query = "SELECT 
                                u.*,
                                d.departmentName
                                FROM users u
                                JOIN departments d on d.departmentIdx = u.departmentIdx
                                WHERE userIdx = :userIdx LIMIT 1";
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

    /**
     * User 삭제 (논리적!)
     * @param string $userEmail
     * @return bool
     */
    public function delete(string $userEmail): bool {
        try {
            $query = "update users set deleted_at = NOW() where userEmail =:userEmail";
            return $this->conn->prepare($query)->execute([
                'userEmail' => $userEmail
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function countPhysicalDeletedTarget() {
        try {
            $query = "SELECT COUNT(*) as rowCount FROM users WHERE DATEDIFF(NOW(), deleted_at) >= 30";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteByCron(): bool
    {
        try {
            $query = "DELETE FROM users WHERE DATEDIFF(NOW(), deleted_at) >= 30";
            return $this->conn->prepare($query)->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}