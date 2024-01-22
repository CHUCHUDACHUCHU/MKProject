<?php
namespace Controller;
use Model\Comment;

/**
 * CommentCotroller
 * Route에서 컨트롤러 사용.
 * 댓글 관련 액션
 */

class CommentController extends BaseController{
    private $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    /**
     * 댓글 생성 기능을 담당
     * @return void
     */
    public function create()
    {
        $postIdx = $_POST['postIdx'];
        $userIdx = $_POST['userIdx'];
        $content = $_POST['content'];
        $parentIdx = $_POST['parentIdx'];

        if ($this->parametersCheck($postIdx, $userIdx, $content)) {
            if (empty($parentIdx)) {
                if ($this->comment->create($postIdx, $userIdx, $content)) {
                    $this->redirect('/mk-board/post/read?postIdx=' . $postIdx, '댓글이 작성되었습니다.');
                } else {
                    $this->redirectBack('댓글 작성에 실패했습니다.');
                }
            } else {
                if ($this->comment->subReplyCreate($postIdx, $parentIdx, $userIdx, $content)) {
                    $this->redirect('/mk-board/post/read?postIdx=' . $postIdx, '댓글이 작성되었습니다.');
                } else {
                    $this->redirectBack('댓글 작성에 실패했습니다.');
                }
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다!');
        }
    }

    /**
     * 댓글 수정 기능을 담당
     * @return void
     */
    public function update() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $commentIdx = $requestData['commentIdx'];
        $content = $requestData['content'];

        $targetComment = $this->comment->getCommentById($commentIdx);

        $result = [
            'status' => '',
            'message' => '',
            'postIdx' => $targetComment['postIdx']
        ];
        if($targetComment['userIdx'] !== $_SESSION['userIdx']) {
            $result['status'] = 'fail';
            $result['message'] = '댓글 수정 권한이 없습니다.';
        } else {
            if($this->parametersCheck($commentIdx, $content)) {
                if($this->comment->update($commentIdx, $content)) {
                    $result['status'] = 'success';
                    $result['message'] = '댓글 수정에 성공하였습니다.';
                } else {
                    $result['status'] = 'fail';
                    $result['message'] = 'DB 변경에 실패하였습니다.';
                }
            } else {
                $result['status'] = 'fail';
                $result['message'] = '입력되지 않은 값이 있습니다.';
            }
        }
        $this->echoJson(['result' => $result]);
    }
    
    
    /**
     * 댓글 삭제 기능을 담당
     * @return void
     */
    public function delete() {
        $requestData = json_decode(file_get_contents("php://input"), true);
        $commentIdx = $requestData['commentIdx'];

        $targetComment = $this->comment->getCommentById($commentIdx);

        $result = [
            'status' => '',
            'message' => '',
            'postIdx' => $targetComment['postIdx']
        ];
        if($targetComment['userIdx'] !== $_SESSION['userIdx']) {
            $result['status'] = 'fail';
            $result['message'] = '댓글 삭제 권한이 없습니다.';
        } else {
            if($this->parametersCheck($commentIdx)) {
                if($this->comment->delete($commentIdx)) {
                    $result['status'] = 'success';
                    $result['message'] = '댓글이 삭제되었습니다.';
                } else {
                    $result['status'] = 'fail';
                    $result['message'] = 'DB 변경에 실패하였습니다.';
                }
            } else {
                $result['status'] = 'fail';
                $result['message'] = '입력되지 않은 값이 있습니다.';
            }
        }
        $this->echoJson(['result' => $result]);
    }
}