<?php

namespace Migration;

use DB\Connection;
use PDOException;

class Log {
    private $conn;

    public function __construct()
    {
        $this->conn = new connection();
        $this->conn = $this->conn->getConnection();
    }

    function migrate() {

        try {
            $tableName = "logs";

            // 테이블이 존재하는지 확인
            $checkTableExists = $this->conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0;

            // 테이블이 존재하지 않으면 테이블 생성
            if (!$checkTableExists) {
                $createTableSQL = "CREATE TABLE $tableName (
                                        logIdx INT AUTO_INCREMENT PRIMARY KEY,
                                        userIdx INT UNSIGNED DEFAULT NULL,
                                        userName VARCHAR(10) DEFAULT NULL,
                                        targetIdx INT UNSIGNED DEFAULT NULL,
                                        targetClass VARCHAR(50) DEFAULT NULL,
                                        actionFunc VARCHAR(100) DEFAULT NULL,
                                        actionType VARCHAR(20) DEFAULT NULL,
                                        updateStatus VARCHAR (10) DEFAULT NULL,
                                        created_at	TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
}