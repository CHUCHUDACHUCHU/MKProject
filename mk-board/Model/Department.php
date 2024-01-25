<?php
namespace Model;

use PDO;
use PDOException;

class Department extends BaseModel {
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllDepartments(): array
    {
        try {
            $query = "SELECT d.*
                        FROM departments d
                        ORDER BY departmentIdx DESC
                     ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}