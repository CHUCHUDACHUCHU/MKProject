<?php
namespace Controller;

use Model\Comment;
use Model\Post;
use Model\File;
use Model\User;

/**
 * PostController
 * Route에서 컨트롤러 사용.
 * 게시글 관련 액션
 */
class PostController extends BaseController {
    private Post $post;
    private Comment $comment;
    private File $file;
    private User $user;

    public function __construct() {
        $this->post = new Post();
        $this->comment = new Comment();
        $this->file = new File();
        $this->user = new User();
    }

    /**
     * Post 생성하기 By Fetch
     * 파일 생성이 같이 일어나도록 구현
     */
    public function createByFetch() {
        /* body 값 */
        $requestData = json_decode(file_get_contents("php://input"), true);
        $title = $requestData['title'];
        $content = $requestData['content'];
        $commonOrNotifyRadio = $requestData['commonOrNotifyRadio'];

        $result = [
            'status' => '',
            'message' => ''
        ];

        $userIdx = $_SESSION['userIdx'];
        $nowUser = $this->user->getUserById($_SESSION['userIdx']);

        if($this->parametersCheck($userIdx, $title, $content, $commonOrNotifyRadio)) {
            $postIdx = $this->post->create($userIdx, $title, $content, $commonOrNotifyRadio);
            if($postIdx) {
                // 로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $postIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__);
                
                $result['status'] = 'success';
                $result['message'] = '게시글 DB 생성에 성공하였습니다.';
                $result['postIdx'] = $postIdx;
                $result['userStatus'] = $nowUser['userStatus'];
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
     * Post 수정하기 By Fetch
     * 파일 수정도 같이!
     */
    public function updateByFetch() {
        /* body 값 */
        $requestData = json_decode(file_get_contents("php://input"), true);
        $title = $requestData['title'];
        $content = $requestData['content'];
        $postIdx = $requestData['postIdx'];

        $result = [
            'status' => '',
            'message' => ''
        ];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);

        if($this->parametersCheck($postIdx, $title, $content)) {
            if($this->post->update($postIdx, $title, $content)) {
                //로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $postIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__);

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
     * 게시글 권한 변경 요청
     */
    public function updateStatus() {
        /* body 값 */
        $requestData = json_decode(file_get_contents("php://input"), true);
        $postStatus = $requestData['postStatus'];
        $postIdx = $requestData['postIdx'];
        $targetPost = $this->post->getPostById($postIdx);

        $result = [
            'status' => '',
            'message' => ''
        ];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);
        $new = [
            'postStatus'=>$postStatus,
        ];
        $new = implode(',', $new);

        $og = [
            'postStatus'=>$targetPost['postStatus'],
        ];
        $og = implode(',', $og);

        $details = "original : " . $og . "\n" . "new : " . $new;


        if($this->parametersCheck($postStatus, $nowUser['userIdx'],  $postIdx)) {
            if($this->post->updateStatus($postStatus, $nowUser['userIdx'], $postIdx)) {
                //로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $postIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__,
                                        updateStatus: $postStatus,
                                        details: $details);

                $result['status'] = 'success';
                $result['message'] = '게시글 권한 변경에 성공하였습니다.';
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
     * Post 삭제하기 (논리적!)
     */
    public function delete() {
        /* body 값 */
        $postIdx = $_POST['postIdx'];

        $nowUser = $this->user->getUserById($_SESSION['userIdx']);

        if ($this->parametersCheck($postIdx)) {
            if ($this->post->delete($postIdx)) {
                $this->comment->deleteCommentsByPost($postIdx);
                $this->file->deleteFilesByPost($postIdx);

                //로깅
                $this->assembleLogData( userIdx: $nowUser['userIdx'],
                                        userName: $nowUser['userName'],
                                        targetIdx: $postIdx,
                                        targetClass: get_class($this),
                                        actionFunc: __METHOD__);

                $this->redirect('/mk-board/post/lists', '');
            }else {
                $this->redirectBack('글 작성에 실패했습니다.');
            }
        } else {
            $this->redirectBack('입력되지 않은 값이 있습니다.');
        }
    }
}