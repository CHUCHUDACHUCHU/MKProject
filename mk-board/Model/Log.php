<?php
namespace Model;

use PDO;
use PDOException;

class Log extends BaseModel {
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 로깅 만들기
     * @param $actionType
     * @param $userIdx
     * @param $userName
     * @param $targetIdx
     * @param $targetClass
     * @param $actionFunc
     * @param $updateStatus
     * @param $details
     * @return bool
     */
    public function create($actionType, $userIdx, $userName, $targetIdx, $targetClass, $actionFunc, $updateStatus, $details): bool
    {
        try {
            $query = "INSERT INTO logs (actionType, userIdx, userName, targetIdx, targetClass, actionFunc, updateStatus, details) 
                                VALUES (:actionType, :userIdx, :userName, :targetIdx, :targetClass, :actionFunc, :updateStatus, :details)";
            return $this->conn->prepare($query)->execute([
                'userIdx' => $userIdx,
                'userName' => $userName,
                'targetIdx' => $targetIdx,
                'targetClass' => $targetClass,
                'actionFunc' => $actionFunc,
                'actionType' => $actionType,
                'updateStatus' => $updateStatus,
                'details' => $details
            ]);
        } catch (PDOException  $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}