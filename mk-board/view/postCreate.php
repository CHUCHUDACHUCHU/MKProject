<!doctype html>
<?php
include "part/head.php";
?>
<body>
    <?php
    include "part/nav.php";
    ?>
<div class="m-4">
    <div class="container mt-5">
        <h4 class="d-inline">view/createPost 파일!!!</h4>
        <p class="mt-1">글을 작성하는 공간입니다.</p>

        <form action="/mk-board/post/create" method="post">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" placeholder="제목을 입력하세요">
            </div>

            <div class="form-group">
                <label for="content">내용</label>
                <textarea class="form-control" name="content" rows="5" placeholder="내용을 입력하세요"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">완료</button>
        </form>
    </div>

</div>
</body>
</html>