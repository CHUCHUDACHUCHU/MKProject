<!doctype html>
<?php
include "part/head.php";
?>
<body>
<div class="m-4">
    <div class="container mt-5">
        <h3 class="d-inline"><a href="/mk-board">MK게시판</a></h3>/<h4 class="d-inline">글 작성</h4>
        <p class="mt-1">글을 작성하는 공간입니다.</p>

        <form action="/mk-board/post/create" method="post">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" placeholder="제목을 입력하세요">
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="userIdx">userIdx</label>
                    <input type="text" class="form-control" name="userIdx" placeholder="일단 userIdx 자기가 입력">
                </div>
            </div>

            <div class="form-group">
                <label for="content">내용</label>
                <textarea class="form-control" name="content" rows="5" placeholder="내용을 입력하세요"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">글쓰기</button>
        </form>
    </div>

</div>
</body>
</html>