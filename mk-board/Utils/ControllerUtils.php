<?php
namespace Utils;

trait ControllerUtils   {
    /**
     * 경로로 이동하고 메시지를 출력
     * @param $path string 주소
     * @param $message string 메시지
     * @return void
     */
    public function redirect(string $path, string $message)
    {
        echo "<script>
                alert('$message');
                location.href='$path';
              </script>";
        exit();
    }

    /**
     * 이전 페이지로 이동하고 메시지를 출력
     * @param $message string 메시지
     * @return void
     */
    public function redirectBack($message)
    {
        echo "<script>
                alert('$message');
                history.back();
              </script>";
        exit();
    }

    /**
     * json 형식으로 출력 (Ajax 요청에 반환 값)
     * @param $data array|object
     * @return void
     */
    public function echoJson($data){
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * 파라미터가 존재 여부 확인
     * @param ...$parameters string 검사할 파라미터
     * @return bool
     */
    public function parametersCheck(...$parameters): bool
    {
        foreach ($parameters as $parameter){
            if (empty($parameter)){
                return false;
            }
        }
        return true;
    }
}