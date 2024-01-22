<!doctype html>
<?php
use Model\Post;
use Model\Comment;
include __DIR__ . '/../part/head.php';
?>
<body>
<?php
include __DIR__ . '/../part/nav.php';
?>


<div class="m-4">
    <div class="container mt-5">
        <h4 class="d-inline">view/postRead 파일!</h4>
        <p class="mt-1 mb-3">글의 상세 내용입니다!!!</p>
        <hr/>
        <?php
        $postIdx = $_GET['postIdx'];
        $post = new Post();
        $comment = new Comment();

        $postInfo = $post->getPost($postIdx);

        if ($postInfo) {
            // 게시글의 락 여부와 락 쿠키 체크
            if ($postInfo['lock'] == 1) {
                ?>
                <form action="./lock/update" method="post">
                    <p>관리자의 승인이 필요합니다. 관리자이시면 아래에 email과 pw를 입력해주세요.</p>
                    <p>혹은? 그냥 승인 버튼있음, 근데 관리자가 아니라면 alert!</p>
                    <div class="form-group">
                        <label for="pw">Password</label>
                        <input id="pw" type="text" class="form-control" name="pw" placeholder="비밀번호를 입력하세요">
                    </div>
                    <button type="submit" class="btn btn-primary">확인하기</button>
                    <a href="/mk-board/post/list" class="btn btn-secondary">목록</a>
                </form>
                <?php
            } else {
                $viewsBonus = 0;
                if (!isset($_COOKIE['post_views' . $postIdx])) {
                    $post->increaseViews($postIdx);
                    $viewsBonus = 1;
                }
                ?>
                <div>
                    <h5 class="d-inline">제목) <?= htmlspecialchars($postInfo['title']) ?></h5>
                    <p class="float-right">글쓴이) <?= $postInfo['userName'] ?></p>
                </div>
                <span class="mr-2">작성일: <?= $postInfo['created_at'] ?></span>
                <span class="mr-2">수정일: <?= $postInfo['updated_at'] ?></span>
                <span class="mr-2">조회수: <?= $postInfo['views'] + $viewsBonus ?></span>
                <hr/>

                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text">
                            <?= nl2br(htmlspecialchars($postInfo['content'])) ?>
                        </p>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/mk-board/post/update?postIdx=<?= $postInfo['postIdx'] ?>" class="btn btn-primary mr-2">수정하기</a>
                    <form action="/mk-board/post/delete" method="post" class="mr-2">
                        <input type="hidden" name="postIdx" value="<?= $postInfo['postIdx'] ?>">
                        <button type="submit" class="btn btn-primary" style="background-color: orangered !important;">삭제하기</button>
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
                                        <?= nl2br($commentInfo['content']) ?>
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

                <div class="mt-2">
                    <hr/>
                    <h5>댓글 작성</h5>
                    <form action="/mk-board/comment/create" method="post">
                        <div class="form-group">
                            <input type="hidden" name="postIdx" id="postIdx" value="<?= $postInfo['postIdx'] ?>">
                            <input type="hidden" name="userIdx" id="userIdx" value="<?= $_SESSION['userIdx'] ?>">
                            <label for="content">내용:</label>
                            <textarea name="content" class="form-control" id="content" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">댓글 작성</button>
                    </form>
                </div>
        <?php
            }
        } else {
            echo "<script>alert('존재하지 않는 게시물입니다.');history.back();</script>";
        }
        ?>
    </div>
</body>
</html>
