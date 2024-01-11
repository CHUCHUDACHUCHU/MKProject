<?php
namespace DB;
use PDO;
use PDOException;

class Connection {
    public static $conn = null;

    public static function getConnection(): PDO
    {
        // if 문 내부는 $this->conn 문제 있을 시 새로.
        if(self::$conn == null) {
            try {
                $config = parse_ini_file(__DIR__ . '/../config.ini');
                echo "<script>alert('dbconn null???')</script>";
                //db 연결
                $dsn = "mysql:host={$config['DB_HOSTNAME']};charset=utf8mb4";
                self::$conn = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //db 존재 확인
                $result = self::$conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$config['DB_NAME']}'");

                //없으면 db생성 및 use DB
                if($result->rowCount() == 0) {
                    self::$conn->exec("CREATE DATABASE {$config['DB_NAME']}");
                    echo "Database {$config['DB_NAME']} created successfully.\n";
                }
                self::$conn->query("use " . $config['DB_NAME']);

            }catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        // 이상 없으니까 그대로~
        return self::$conn;
    }
}