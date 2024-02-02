<?php
namespace Model;

use DB\Connection;

class BaseModel {
    protected \PDO $conn;

    public function __construct() {
        $this->conn = Connection::getConnection();
    }
}