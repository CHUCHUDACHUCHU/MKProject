<?php

namespace Migration;

use DB\Connection;
use PDOException;

class User
{
    private $conn;
    private false|array $config;

    public function __construct()
    {
        $this->conn = new connection();
        $this->conn = $this->conn->getConnection();
        $this->config = parse_ini_file(__DIR__ . '/../../config.ini');
    }

    function migrate()
    {
        try {
            $tableName = "users";

            // 테이블이 존재하는지 확인
            $checkTableExists = $this->conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0;

            // 테이블이 존재하지 않으면 테이블 생성
            if (!$checkTableExists) {
                $createTableSQL = "CREATE TABLE $tableName (
                                        userIdx	INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                        userName	VARCHAR(10)	NOT NULL,
                                        userEmail	VARCHAR(30)	NOT NULL,
                                        userPw	VARCHAR(100),
                                        departmentIdx	INT DEFAULT 1,
                                        userStatus  VARCHAR(10) DEFAULT '대기',
                                        userInit    INT DEFAULT 0,
                                        userPhone	VARCHAR(30),
                                        created_at	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                        updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                        deleted_at DATETIME DEFAULT NULL
        )";
                $this->conn->exec($createTableSQL);
                echo "Table $tableName created successfully\n";
            } else {
                echo "Table $tableName already exists\n";
            }
        } catch
        (PDOException $e) {
            echo "Connection failed: " . $e->getMessage()."\n";
        }
    }

    function createAdmin() {
        try {
            $tableName = "users";

            $salt = $this->config['PASSWORD_SALT'];
            $passwordInit = $this->config['PASSWORD_INIT'];
            $userPw = crypt($passwordInit, $salt);

            $checkAdminExists = $this->conn->query("SELECT * FROM users WHERE userName='admin'")->rowCount() > 0;

            if (!$checkAdminExists) {
                $createAdmin = "INSERT INTO users (userName, userEmail, userPw, departmentIdx, userStatus, userInit, userPhone)
                                            VALUES('admin', 'admin', :userPw, 7, '관리자', '1', '010-6630-7548');";
                $stmt = $this->conn->prepare($createAdmin);
                $stmt->bindParam('userPw', $userPw);
                $stmt->execute();
                echo "Admin user created successfully\n";
            } else {
                echo "Admin user already exists\n";
            }
        } catch
        (PDOException $e) {
            echo "Connection failed: " . $e->getMessage()."\n";
        }
    }
}