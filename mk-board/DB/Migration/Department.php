<?php

namespace Migration;

use DB\Connection;
use PDOException;

class Department {
    private $conn;

    public function __construct()
    {
        $this->conn = new connection();
        $this->conn = $this->conn->getConnection();
    }

    function migrate() {

        try {
            $tableName = "departments";

            // 테이블이 존재하는지 확인
            $checkTableExists = $this->conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0;

            // 테이블이 존재하지 않으면 테이블 생성
            if (!$checkTableExists) {
                $createTableSQL = "CREATE TABLE $tableName (
                                        departmentIdx   INT AUTO_INCREMENT PRIMARY KEY,
                                        departmentName  VARCHAR(10)	NOT NULL
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

    function createDefaultDepartment() {
        try {
            $tableName = "departments";

            // 테이블이 존재하는지 확인
            $checkDefaultDepartmentExists = $this->conn->query("SELECT * FROM $tableName")->rowCount() == 18;

            // 테이블이 존재하면 기본 부서 생성
            if (!$checkDefaultDepartmentExists) {
                $createAdmin = "INSERT INTO $tableName (departmentName) values 
												('미지정'),
												('매경닷컴'),
												('플랫폼개발국'),
												('경영관리국'),
												('DC국'),
												('DM전략국'),
												('개발부'),
												('시스템보안팀'),
												('R&D팀'),
												('디자인팀'),
												('DM전략팀'),
												('MBN뉴스편집팀'),
												('MBN동영상팀'),
												('MK뉴스편집팀'),
												('기업대응팀'),
												('이슈대응팀'),
												('스타투데이팀'),
												('디지털뉴스룸');";
                $this->conn->exec($createAdmin);
                echo "Default Department created successfully\n";
            } else {
                echo "Default Department already exists\n";
            }
        } catch
        (PDOException $e) {
            echo "Connection failed: " . $e->getMessage()."\n";
        }
    }
}