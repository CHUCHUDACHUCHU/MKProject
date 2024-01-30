<?php
namespace Controller;
use Model\Comment;
use Model\Post;
use Model\User;

/**
 * CommentCotroller
 * Route에서 컨트롤러 사용.
 * 댓글 관련 액션
 */

class CommentController extends BaseController{
    private Comment $comment;
    private Post $post;
    private User $user;

    public function __construct() {
        $this->comment = new Comment();
        $this->post = new Post();
        $this->user = new User();
    }

    /**
     * 댓글 생성 기능을 담당
     * @return void
     */
    public function create()
    {
        /* body 값 */
        $postIdx = $_POST['postIdx'];
        $content = $_POST['content'];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);
        $targetPost = $this->post->getPostById($postIdx);

        if ($this->parametersCheck($postIdx, $nowUser['userIdx'], $content)) {
            $commentIdx = $this->comment->create($postIdx, $nowUser['userIdx'], $content);
            if ($commentIdx) {
                //로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $commentIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__);

                if(isset($_POST['reject'])) {
                    $new = [
                        'postStatus'=>'반려',
                    ];
                    $new = implode(',', $new);

                    $og = [
                        'postStatus'=>$targetPost['postStatus'],
                    ];
                    $og = implode(',', $og);

                    $details = "original : " . $og . "\n" . "new : " . $new;
                    //로깅
                    $this->assembleLogData( userIdx: $nowUser['userIdx'],
                        userName: $nowUser['userName'],
                        targetIdx: $postIdx,
                        targetClass: 'Controller\PostController',
                        actionFunc: 'Controller\PostController::updateStatus',
                        updateStatus: '반려',
                        details: $details);

                    $this->post->updateStatus('반려', $nowUser['userIdx'], $postIdx);
                    $this->redirectBack('게시글 권한이 반려되었습니다.');
                } else {
                    $this->redirect('/mk-board/post/read?postIdx=' . $postIdx, '');
                }
            } else {
                $this->redirectBack('댓글 작성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다!');
        }
    }

    /**
     * 댓글 수정 기능을 담당
     * @return void
     */
    public function update()
    {
        /* body 값 */
        $requestData = json_decode(file_get_contents("php://input"), true);
        $commentIdx = $requestData['commentIdx'];
        $content = $requestData['content'];

        $result = [
            'status' => '',
            'message' => '',
        ];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);
        $targetComment = $this->comment->getCommentById($commentIdx);

        $new = [
            'commentContent' => $content
        ];
        $new = implode(',', $new);

        $og = [
            'commentContent' => $targetComment['content']
        ];
        $og = implode(',', $og);

        $details = "original : " . $og . "\n" . "new : " . $new;



        if ($this->parametersCheck($commentIdx, $content)) {
            if ($this->comment->update($commentIdx, $content)) {
                //로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $commentIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__,
                                        details: $details);

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
        $this->echoJson(['result' => $result]);
    }
    
    
    /**
     * 댓글 삭제 기능을 담당
     * @return void
     */
    public function delete() {
        /* body 값*/
        $requestData = json_decode(file_get_contents("php://input"), true);
        $commentIdx = $requestData['commentIdx'];

        $result = [
            'status' => '',
            'message' => '',
        ];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);

        if($this->parametersCheck($commentIdx)) {
            if($this->comment->delete($commentIdx)) {
                //로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $commentIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__);

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
        $this->echoJson(['result' => $result]);
    }
}