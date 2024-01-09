<?php
//namespace MKBoard\Model;
//
//use PDO;
//use PDOException;
//
//class User extends BaseModel {
//    public function __construct()
//    {
//        parent::__construct();
//    }
//
//    // 회원 생성하기(현재는 회원가입, 추후 관리자가 생성하는 기능으로 변경 예정) DB 발신
//    public function create($userName, $userEmail, $userPw, $userDepart, $userPhone): bool {
//        try {
//            $salt = '$5$QOPrAVIK'."$userEmail".'$';
//            $hashPw = crypt($userPw, $salt);
//            $query = 'INSERT INTO userTB (userName, userEmail, userPw, userDepart, userPhone)
//                                    VALUES("$userName", "$userEmail", "$hashPw", "$userDepart", "$userPhone")';
//            return $this->conn->prepare($query)->execute([
//                'userName' => $userName,
//                'userEmail' => $userEmail,
//                'userPw' => $hashPw,
//                'userDepart' => $userDepart,
//                'userPhone' => $userPhone
//            ]);
//        }catch (PDOException $e) {
//            error_log($e->getMessage());
//            return false;
//        }
//    }
//
//    // 회원 데이터 가져오기
//    public function getUser($userEmail) {
//        try {
//            $query = 'SELECT * FROM userTB WHERE userEmail = :userEmail LIMIT 1';
//            $stmt = $this->prepare($query);
//            $stmt->excute([
//                'userEmail' => $userEmail
//            ]);
//            return $stmt->fetch();
//
//        }catch (PDOException $e) {
//            error_log($e->getMessage());
//            return false;
//        }
//    }
//}