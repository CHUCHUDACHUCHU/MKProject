<?php
namespace Controller;

use Model\Post;

class PostController extends BaseController {
    private $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function create() {
        $userIdx = $_POST['userIdx'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        // 데이터 유효성 검사
        if ($this->parametersCheck($userIdx,$title,$content)) {
            // POST 데이터 생성
            if ($this->post->create($userIdx, $title, $content)) {
                $this->redirect('/mk-board', '글이 작성되었습니다.');
            } else {
                $this->redirectBack('글 작성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }
}