<?php

namespace Migration;

use DB\Connection;
use PDOException;

class File {
    private $conn;

    public function __construct()
    {
        $this->conn = new connection();
        $this->conn = $this->conn->getConnection();
    }

    function migrate() {

        try {
            $tableName = "files";

            // 테이블이 존재하는지 확인
            $checkTableExists = $this->conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0;

            // 테이블이 존재하지 않으면 테이블 생성
            if (!$checkTableExists) {
                $createTableSQL = "CREATE TABLE $tableName (
                                        fileIdx INT AUTO_INCREMENT PRIMARY KEY,
                                        postIdx INT UNSIGNED DEFAULT NULL ,
                                        userIdx INT UNSIGNED DEFAULT NULL ,
                                        FOREIGN KEY (postIdx) REFERENCES posts(postIdx),
                                        fileName VARCHAR(255),
                                        fileOriginName VARCHAR(255),
                                        fileSize VARCHAR(100),
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
}