<!doctype html>
<?php
use Model\Post;

$post = new Post();
$postInfo = $post->getPost($_GET['postIdx']);
include "part/head.php";
?>
<body>
<?php
include "part/nav.php";
?>
<div class="m-4">
    <div class="container mt-5">
        <h4 class="d-inline">view/updatePost 파일!!!</h4>
        <p class="mt-1">글을 수정하는 공간입니다.</p>

        <form action="/mk-board/post/update" method="post">
            <input type="hidden" name="postIdx" value="<?= $_GET['postIdx'] ?>">

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" placeholder="제목을 입력하세요" value="<?= $postInfo['title'] ?>">
            </div>

            <div class="form-group">
                <label for="content">내용</label>
                <textarea class="form-control" name="content" rows="5" placeholder="내용을 입력하세요"><?= $postInfo['content'] ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">수정하기</button>
        </form>
    </div>

</div>
</body>
<script src="../assets/js/index.js"></script>
</html>