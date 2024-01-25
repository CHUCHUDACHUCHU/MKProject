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
        $display = 'block';
        $LockContent = 'none';
        $postIdx = $_GET['postIdx'];
        $post = new Post();
        $comment = new Comment();
        $file = new File();
        $user = new User();

        $postInfo = $post->getPostById($postIdx);
        $nowUser = $user->getUserById($_SESSION['userIdx']);
        if($postInfo['lock']) {
            $display = 'none';
            $LockContent = 'block';
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
            </div>
            <hr/>
            <div class="">
                <small class="mr-2">작성일: <?= $postInfo['created_at'] ?></small>
                <small class="mr-2">수정일: <?= $postInfo['updated_at'] ?></small>
                <small class="mr-2">조회수: <?= $postInfo['views'] + $viewsBonus ?></small>
            </div>
            <hr/>

            <div class="card mb-3" style="background-color: oldlace; display: <?= $display ?>">
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
                            <tr class="fileList">
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
            <div class="card mb-3" style="min-height: 300px; display: <?= $display ?>;">
                <div class="card-body">
                    <p class="card-text">
                        <?= nl2br(htmlspecialchars($postInfo['content'])) ?>
                    </p>
                </div>
            </div>
            <div class="card mb-3" style="min-height: 300px;
                                            display: <?= $LockContent ?>;
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



            <?php
            if($_SESSION['userIdx'] === $postInfo['userIdx']) {
                ?>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/mk-board/post/update?postIdx=<?= $postInfo['postIdx'] ?>" class="btn btn-primary mr-2" style="display: <?= $display ?>">수정하기</a>
                    <form action="/mk-board/post/delete" method="post" class="mr-2 deletePostBtn">
                        <input type="hidden" name="postIdx" value="<?= $postInfo['postIdx'] ?>">
                        <button type="submit" class="btn btn-danger">삭제하기</button>
                    </form>
                    <a href="/mk-board/post/list" class="btn btn-primary mr-2">목록</a>
                </div>
            <?php
            }
            ?>

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
                                <button style="height: 35px;" type="button"
                                        class="btn-close btn btn-primary p-1"
                                        data-bs-dismiss="modal" aria-label="Close">
                                    <span style="width: 23px; height: 23px"
                                          class="material-symbols-outlined">close</span>
                                </button>
                            </div>
                            <input type="hidden" class="commentIdx" id="commentIdx" value="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Name:</label>
                                    <input class="form-control" type="txt" id="userName" value="" readonly>
                                </div><div class="mb-3">
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            if($nowUser['userStatus'] === '관리자') {
                ?>
                <div class="mt-2">
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
            }
            ?>
        <?php
        } else {
            echo "<script>alert('존재하지 않는 게시물입니다.');history.back();</script>";
        }
        ?>
    </div>
</body>
</html>
