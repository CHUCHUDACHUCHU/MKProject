<?php
namespace DB;
use PDO;
use PDOException;

class Connection {
    private $conn = null;
    private $config;

    public function __construct() {
        $this->config = parse_ini_file(__DIR__ . '/../config.ini');
    }

    public function getConnection() {
        // if 문 내부는 $this->conn 문제 있을 시 새로.
        if($this->conn == null) {
            try {
                //db 연결
                $dsn = "mysql:host={$this->config['DB_HOSTNAME']};charset=utf8";
                $conn = new PDO($dsn, $this->config['DB_USER'], $this->config['DB_PASSWORD']);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //db 존재 확인
                $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->config['DB_NAME']}'");

                //없으면 db생성 및 use DB
                if($result->rowCount() == 0) {
                    $conn->exec("CREATE DATABASE {$this->config['DB_NAME']}");
                    echo "Database {$this->config['DB_NAME']} created successfully.\n";
                }
                $conn->query("use " . $this->config['DB_NAME']);

            }catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }

            //새로 생성한 connection으로 this 지정.
            $this->conn = $conn;
        }
        // 이상 없으니까 그대로~
        return $this->conn;
    }
}