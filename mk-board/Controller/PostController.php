<?php
namespace Controller;

use Model\Comment;
use Model\Post;
use Model\File;

/**
 * PostController
 * Route에서 컨트롤러 사용.
 * 게시글 관련 액션
 */
class PostController extends BaseController {
    private $post;
    private $comment;
    private $file;

    public function __construct() {
        $this->post = new Post();
        $this->comment = new Comment();
        $this->file = new File();
    }

    /**
     * Post 생성하기 By Fetch
     * 파일 생성이 같이 일어나도록 구현
     */
    public function createByFetch() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $title = $requestData['title'];
        $content = $requestData['content'];
        $userIdx = $_SESSION['userIdx'];

        $result = [
            'status' => '',
            'message' => ''
        ];

        if($this->parametersCheck($userIdx, $title, $content)) {
            $postIdx = $this->post->create($userIdx, $title, $content);
            if($postIdx) {
                $result['status'] = 'success';
                $result['message'] = '게시글 DB 생성에 성공하였습니다.';
                $result['postIdx'] = $postIdx;
            } else {
                $result['status'] = 'fail';
                $result['message'] = '게시글 DB 생성에 실패하였습니다.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = '입력되지 않은 값이 있습니다.';
        }
        $this->echoJson(['result' => $result]);
    }
    
    /**
     * Post 생성하기 By Form
     * 그냥 게시글만 생성.
     */
    public function createByForm()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $userIdx = $_SESSION['userIdx'];

        if ($this->parametersCheck($userIdx, $title, $content)) {
            $postIdx = $this->post->create($userIdx, $title, $content);
            if ($postIdx) {
                $this->redirect('/mk-board/post/read?postIdx=' . $postIdx, '');
            } else {
                $this->redirectBack('댓글 작성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다!');
        }
    }

    /**
     * Post 수정하기 By Fetch
     * 파일 수정도 같이!
     */
    public function updateByFetch() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $title = $requestData['title'];
        $content = $requestData['content'];
        $postIdx = $requestData['postIdx'];

        $result = [
            'status' => '',
            'message' => ''
        ];

        if($this->parametersCheck($postIdx, $title, $content)) {
            if($this->post->update($postIdx, $title, $content)) {
                $result['status'] = 'success';
                $result['message'] = '게시글 수정에 성공하였습니다.';
            } else {
                $result['status'] = 'fail';
                $result['message'] = '게시글 수정에 실패하였습니다.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = '입력되지 않은 값이 있습니다.';
        }
        $this->echoJson(['result' => $result]);
    }

    /**
     * Post 수정하기 By Form
     * 파일 수정 따로 안 할 때!
     */
    public function updateByForm() {
        $postIdx = $_POST['postIdx'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        if($this->parametersCheck($postIdx, $title, $content)) {
            if($this->post->update($postIdx, $title, $content)) {
                $this->redirect('/mk-board/post/read?postIdx=' . $postIdx, '');
            } else {
                $this->redirectBack('DB 변경에 실패하였습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }

    /**
     * Post 삭제하기 (논리적!)
     */
    public function delete() {
        $postIdx = $_POST['postIdx'];
        // 데이터 유효성 검사
        if ($this->parametersCheck($postIdx)) {
            if ($this->post->delete($postIdx)) {
                $this->comment->deleteCommentsByPost($postIdx);
                $this->file->deleteFilesByPost($postIdx);
                $this->redirect('/mk-board/post/lists', '');
            }else {
                $this->redirectBack('글 작성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }
}