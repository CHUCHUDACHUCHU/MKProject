<!doctype html>
<?php
use Model\Post;
use Model\Comment;
use Model\File;
use Model\User;
include __DIR__ . '/../part/head.php';
?>
<body>
<?php
include __DIR__ . '/../part/nav.php';
?>


<div class="m-4">
    <div class="container mt-5">
        <?php
        $postIdx = $_GET['postIdx'];
        $post = new Post();
        $comment = new Comment();
        $file = new File();
        $user = new User();

        $fileDisplay = 'none';
        $contentDisplay = 'none';
        $refusalDisplay = 'none';
        $changePostStatusDisplay = 'none';
        $updatePostDisplay = 'none';
        $deletePostDisplay = 'none';
        $commentCreateDisplay = 'none';
        $standby = '';
        $approval = '';
        $refusal = '';

        $postInfo = $post->getPostById($postIdx);
        $nowUser = $user->getUserById($_SESSION['userIdx']);


        if($postInfo['postStatus'] === '대기') {
            $standby = 'active';
        } else if($postInfo['postStatus'] === '승인') {
            $approval = 'active';
        } else if($postInfo['postStatus'] === '반려') {
            $refusal = 'active';
        }

        //1. 관리자
        if($nowUser['userStatus'] === '관리자') {
            // 1-1. 본인글 => 공지글~
            if($_SESSION['userIdx'] == $postInfo['userIdx']) {
                $fileDisplay = 'block';
                $contentDisplay = 'block';
                $updatePostDisplay = 'block';
                $deletePostDisplay = 'block';
                $commentCreateDisplay = 'block';

                // 1-2. 다른 일반 글 => 상태관리 가능.
            } else {
                $fileDisplay = 'block';
                $contentDisplay = 'block';
                $changePostStatusDisplay = 'block';
                $commentCreateDisplay = 'block';
            }


            //2. 일반
        } else {
            // 1. 내거 내가 볼 때,
            if($_SESSION['userIdx'] == $postInfo['userIdx']) {
                // 1-1. 승인
                if($postInfo['postStatus'] === '승인') {
                    $fileDisplay = 'block';
                    $contentDisplay = 'block';
                    $deletePostDisplay = 'block';
                    $commentCreateDisplay = 'block';
                    // 1-2. 대기/반려
                } else {
                    $refusalDisplay = 'block';
                    $deletePostDisplay = 'block';
                }
                // 2. 내가 다른 사람 거 볼 때,
            } else {
                // 2-1. 공지글일 때,
                if($postInfo['postStatus'] === '공지') {
                    $fileDisplay = 'block';
                    $contentDisplay = 'block';
                    $commentCreateDisplay = 'block';
                } else {
                    header("Location: /mk-board");
                    exit();
                }
            }
        }


        if ($postInfo) {
            $viewsBonus = 0;
            if (!isset($_COOKIE['post_views' . $postIdx])) {
                $post->increaseViews($postIdx);
                $viewsBonus = 1;
            }
            ?>
            <div>
                <h3 class="d-inline" style="font-weight: bolder">제목) <?= htmlspecialchars($postInfo['title']) ?></h3>
                <h5 class="float-right">작성자) <?= $postInfo['userName'] ?></h5>
                <div class="">
                    <small class="mr-2">작성일: <?= $postInfo['created_at'] ?></small>
                    <small class="mr-2">수정일: <?= $postInfo['updated_at'] ?></small>
                    <small class="mr-2">조회수: <?= $postInfo['views'] + $viewsBonus ?></small>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <div class="btn-group btn-group-toggle" data-toggle="buttons" style="display: <?=$changePostStatusDisplay?>">
                    <label class="btn btn-secondary <?=$standby?>">
                        <input type="radio" name="postStatusOptions" id="option1" value="대기"> 대기
                    </label>
                    <label class="btn btn-secondary <?=$approval?>">
                        <input type="radio" name="postStatusOptions" id="option2" value="승인"> 승인
                    </label>
                    <label class="btn btn-secondary <?=$refusal?>">
                        <input type="radio" class="openRejectMessageModal1" id="openRejectMessageModal1" value="반려"> 반려
                    </label>
                </div>
            </div>

            <hr/>

            <div class="card mb-3" style="background-color: oldlace;">
                <div class="card-body" style="padding: 15px; padding-bottom: 0px">
                    <?php
                    $fileList = $file->getAllFilesByPostIdx($postInfo['postIdx']);
                    // 테이블 형식으로 표시하려면 아래와 같이 수정 가능
                    echo '<table class="table">';
                    echo '<thead>
                            <tr>
                                <th scope="col">번호</th>
                                <th scope="col">파일명</th>
                                <th scope="col">용량</th>
                                <th scope="col" colspan="2">작업</th>
                            </tr>
                          </thead>';
                    echo '<tbody>';

                    foreach ($fileList as $file) {
                        if($file['deleted_at'] !== null) {
                            $disable = '';
                        }
                        ?>
                        <tr class="fileList" style="display: <?=$fileDisplay?>">
                            <td class="fileIdx"><?= $file['fileIdx'] ?></td>
                            <td><a href="" class="downloadATag" style="cursor: pointer"><?= $file['fileOriginName'] ?></a></td>
                            <td><?= $file['fileSize'] ?></td>
                            <td><button type="button" class="btn btn-primary btn-sm downloadBtn">다운로드</button></td>
                        </tr>
                        <?php
                    }
                    echo '</tbody></table>';
                    ?>
                </div>
            </div>
            <br/>

            <div class="card mb-3" style="min-height: 300px; display: <?=$contentDisplay?>">
                <div class="card-body">
                    <p class="card-text">
                        <?= nl2br(htmlspecialchars($postInfo['content'])) ?>
                    </p>
                </div>
            </div>
            <div class="card mb-3" style="min-height: 300px;
                    display: <?=$refusalDisplay?>;
                    position: relative;
                    border: 2px solid #dc3545;
                    border-radius: 10px;
                    background-color: #f8d7da;">
                <div class="card-body" style="padding: 20px; text-align: center;">
                    <p class="card-text" style="font-size: 18px; font-weight: bold; color: #dc3545;">
                        해당 게시글에 승인이 되지 않아 열람이 불가합니다.
                    </p>
                </div>
            </div>

            <div class="modal fade" id="rejectMessageModal" tabindex="-1" aria-labelledby="rejectMessageModal"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="/mk-board/comment/create" method="post" class="rejectMessageModalForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectMessageModalLabel">반려 사유 작성</h5>
                                <input type="hidden" id="modalPostIdx" name="postIdx" value="">
                                <input type="hidden" class="userIdx" name="userIdx" value="<?= $nowUser['userIdx'] ?>">
                                <input type="hidden" name="reject" value="reject">
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Name:</label>
                                    <input class="form-control" type="txt" name="userName" value="<?= $nowUser['userName'] ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email:</label>
                                    <input class="form-control" type="txt" name="userEmail" value="<?= $nowUser['userEmail'] ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="editModalContent" class="form-label">내용:</label>
                                    <textarea class="form-control content" name="content"
                                              rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">완료</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/mk-board/post/update?postIdx=<?= $postInfo['postIdx'] ?>" class="btn btn-primary mr-2" style="display: <?=$updatePostDisplay?>">수정하기</a>
                <form action="/mk-board/post/delete" method="post" class="mr-2 deletePostBtn">
                    <input type="hidden" name="postIdx" value="<?= $postInfo['postIdx'] ?>">
                    <button type="submit" class="btn btn-danger" style="display: <?=$deletePostDisplay?>">삭제하기</button>
                </form>
                <a href="/mk-board/post/list" class="btn btn-primary mr-2">목록</a>
            </div>

            <hr/>
            <h3>댓글</h3>
            <?php
            $comments = $comment->getAllComments($postInfo['postIdx']);
            if ($comments) {
                foreach ($comments as $commentInfo) {
                    ?>
                    <!-- 댓글 섹션 -->
                    <div class="mt-4 card" style="background-color: cornsilk">
                        <div class="card-body commentBox">
                            <input type="hidden" class="commentIdx" value="<?= $commentInfo['commentIdx'] ?>"/>
                            <div class="media-body mb-3">
                                <div class="row" style="padding-left: 10px;">
                                    <h6 class="mt-0 userName"><?= $commentInfo['userName'] ?></h6>
                                    <h6 class="mt-0 userEmail">@ <?= $commentInfo['userEmail'] ?></h6>
                                </div>
                                <div class="mt-2 content" style="background-color: orange; padding: 10px; border-radius: 10px; display: inline-block;">
                                    <?= htmlspecialchars(nl2br($commentInfo['content'])) ?>
                                </div>
                            </div>
                            <?php
                            if($_SESSION['userIdx'] === $commentInfo['userIdx']) {
                                ?>
                                <div class="editCommentBtnGroup">
                                    <button class="btn btn-primary btn-sm openCommentEditModal">수정</button>
                                    <button class="btn btn-danger btn-sm deleteCommentBtn">삭제</button>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="row mt-1" style="padding-left: 15px;">
                                <p class="mb-0" style="font-size: 13px">작성: <?= $commentInfo['created_at'] ?> &nbsp;</p>
                                <p class="mb-0" style="font-size: 13px">수정: <?= $commentInfo['updated_at'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="editCommentModalForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">댓글 수정</h5>
                            </div>
                            <input type="hidden" class="commentIdx" id="commentIdx" value="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Name:</label>
                                    <input class="form-control" type="txt" id="userName" value="" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email:</label>
                                    <input class="form-control" type="txt" id="userEmail" value="" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="editModalContent" class="form-label">내용:</label>
                                    <textarea class="form-control content" id="content"
                                              rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="editCommentModalSubmit" class="btn btn-primary editCommentModalSubmit">수정</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-2" style="display: <?= $commentCreateDisplay?>">
                <hr/>
                <h5>댓글 작성</h5>
                <form action="/mk-board/comment/create" method="post">
                    <div class="form-group">
                        <input type="hidden" name="postIdx" id="postIdx" value="<?= $postInfo['postIdx'] ?>">
                        <input type="hidden" name="userIdx" id="userIdx" value="<?= $_SESSION['userIdx'] ?>">
                        <textarea name="content" class="form-control" id="content" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">댓글 작성</button>
                </form>
            </div>
            <?php
        } else {
            echo "<script>alert('존재하지 않는 게시물입니다.');history.back();</script>";
        }
        ?>
    </div>
</div>
</body>
</html>
