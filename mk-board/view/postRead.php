<!doctype html>
<?php
include "session.php";
use Model\Post;
use Model\User;

$user = new User();
$nowUser = $user->getUserById($_SESSION['userIdx']);


include "part/head.php";
?>
<body>

    <?php
    include "part/nav.php";
    ?>


<div class="m-4">
    <div class="container mt-5">
        <h4 class="d-inline">view/postRead 파일!</h4>
        <p class="mt-1 mb-3">글의 상세 내용입니다!!!</p>
        <hr/>
        <?php
        $postIdx = $_GET['postIdx'];
        $post = new Post();

        $postInfo = $post->getPost($postIdx);

        if ($postInfo) {
            // 게시글의 락 여부와 락 쿠키 체크
            if ($postInfo['lock'] == 1) {
                ?>
                <form action="/mk-board/post/updatePostLock" method="post">
                    <p>관리자의 승인이 필요합니다. 관리자이시면 아래에 email과 pw를 입력해주세요.</p>
                    <p>혹은? 그냥 승인 버튼있음, 근데 관리자가 아니라면 alert!</p>
                    <div class="form-group">
                        <input type="hidden" name="postIdx" value="<?= $postIdx ?>">
                        <label for="pw">Password</label>
                        <input id="pw" type="text" class="form-control" name="pw" placeholder="비밀번호를 입력하세요">
                    </div>
                    <button type="submit" class="btn btn-primary">확인하기</button>
                    <a href="/mk-board" class="btn btn-secondary">목록</a>
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
                    <h5 class="d-inline">제목) <?= $postInfo['title'] ?></h5>
                    <p class="float-right">글쓴이) <?= $postInfo['userName'] ?></p>
                </div>
                <span class="mr-2">작성일: <?= $postInfo['created_at'] ?></span>
                <span class="mr-2">수정일: <?= $postInfo['updated_at'] ?></span>
                <span class="mr-2">조회수: <?= $postInfo['views'] + $viewsBonus ?></span>
                <hr/>

                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text">
                            <?= nl2br($postInfo['content']) ?>
                        </p>
                    </div>
                </div>

                <a href="./update?postIdx=<?= $postInfo['postIdx'] ?>" class="btn btn-primary">수정하기</a>
                <a href="/mk-board" class="btn btn-secondary">목록</a>
                <a href="./delete?postIdx=<?= $postInfo['postIdx'] ?>" class="btn btn-dark">삭제하기</a>
<!--                <button class="btn btn-success" id="thumbsUp">-->
<!--                    추천 --><?//= $postInfo['thumbs_up'] != 0 ? "(" . $postInfo['thumbs_up'] . ")" : '' ?>
<!--                    <span class="material-symbols-outlined" style="font-size:16px">thumb_up</span>-->
<!--                </button>-->
                <!--추천에서 사용할 postIdx 값-->
<!--                <input type="hidden" id="postIdx" value="--><?//= $postIdx ?><!--">-->
                <?php
            }
        } else {
            echo "<script>alert('존재하지 않는 게시물입니다.');history.back();</script>";
        }
        ?>
    </div>
</body>
