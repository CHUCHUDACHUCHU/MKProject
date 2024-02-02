<?php

namespace Migration;

use DB\Connection;
use PDOException;

class Post
{
    private $conn;

    public function __construct()
    {
        $this->conn = new connection();
        $this->conn = $this->conn->getConnection();
    }

    function migrate()
    {
        try {
            $tableName = "posts";

            // 테이블이 존재하는지 확인
            $checkTableExists = $this->conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0;

            // 테이블이 존재하지 않으면 테이블 생성
            if (!$checkTableExists) {
                $createTableSQL = "CREATE TABLE $tableName ( 
                                        postIdx	INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                        userIdx	INT	UNSIGNED,
                                        postStatus  VARCHAR(10) DEFAULT '대기',
                                        statusChangerIdx INT UNSIGNED DEFAULT NULL,
                                        title	VARCHAR(100)	NOT NULL,
                                        content	TEXT	NOT NULL,
                                        views   INT UNSIGNED DEFAULT 0,
                                        created_at	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
}