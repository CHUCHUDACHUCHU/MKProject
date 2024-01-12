<?php
namespace Controller;

use Model\Post;

/**
 * PostController
 * Route에서 컨트롤러 사용.
 * 게시글 관련 액션
 */
class PostController extends BaseController {
    private $post;

    public function __construct() {
        $this->post = new Post();
    }

    /**
     * Post 생성하기
     */
    public function create() {
        $userIdx = $_SESSION['userIdx'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        // 데이터 유효성 검사
        if ($this->parametersCheck($userIdx,$title,$content)) {
            // POST 데이터 생성
            if ($this->post->create($userIdx, $title, $content)) {
                $this->redirect('/mk-board/post/list', '글이 작성되었습니다.');
            } else {
                $this->redirectBack('글 작성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }

    /**
     * Post 수정하기
     */
    public function update() {
        $postIdx = $_POST['postIdx'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        // 데이터 유효성 검사
        if ($this->parametersCheck($postIdx,$title,$content)) {
            // POST 데이터 생성
            if ($this->post->update($postIdx, $title, $content)) {
                $this->redirect('/mk-board/post/list', '글이 수정되었습니다.');
            } else {
                $this->redirectBack('글 작성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }
}