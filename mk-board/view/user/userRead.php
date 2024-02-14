<!DOCTYPE html>
<!doctype html>
<?php
use Model\Post;
use Model\User;
$post = new Post();
$user = new User();
$requireUser = $user->getUserById($_GET['userIdx']);
include __DIR__ . '/../part/head.php';
?>
<body>
<?php
include __DIR__ . '/../part/nav.php';
?>
<!-- Main Content -->
<div class="container mt-4">
    <div class="row">
        <!-- Left Side: User Info -->
        <div class="col-md-3">
            <h3>My Information</h3>
            <img src="/mk-board/assets/img/logo.png" alt="Profile Image" class="img-fluid mb-3">
            <form action="/mk-board/user/update/info" method="post">
                <div class="form-group mb-1">
                    <label>userName</label>
                    <input type="text" class="form-control"  value="<?=$requireUser['userName']?>" name="userName" readonly>
                </div>
                <div class="form-group mb-1">
                    <label>userEmail</label>
                    <input type="text" class="form-control" value="<?=$requireUser['userEmail']?>" name="userEmail" readonly >
                </div>
                <div class="form-group mb-1">
                    <label>userStatus</label>
                    <input type="text" class="form-control" value="<?=$requireUser['userStatus']?>" name="userPhone" readonly>
                </div>
                <div class="form-group mb-1">
                    <label>소속</label>
                    <input type="text" class="form-control" value="<?=$requireUser['departmentName']?>" name="departmentIdx" readonly>
                </div>
                <div class="form-group mb-1">
                    <label>userPhone</label>
                    <input type="text" class="form-control" value="<?=$requireUser['userPhone']?>" name="userPhone" readonly>
                </div>
            </form>
        </div>

        <!-- Right Side: My Posts List -->
        <div class="col-md-8">
            <h3>My Posts</h3>
            <!-- Your code to display the list of posts goes here -->
            <div class="m-4">
                <div id="write_btn" class="mb-4">
                    <a href="/mk-board/post/create">
                        <button class="btn btn-primary float-right">글쓰기</button>
                    </a>
                </div>

                <!--검색-->
                <div class="container mt-4 mb-3">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <div class="form-inline full-width-form">
                                    <div class="form-group mb-2 flex-fill">
                                        <label for="searchInput" class="sr-only">검색</label>
                                        <input name="search" type="text" class="form-control w-100" id="searchInput"
                                               placeholder="Search"
                                               value="<?= $_GET['search'] ?? '' ?>">
                                    </div>
                                    <button id="searchSubmit" type="submit" class="btn btn-primary mb-2">검색</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- 게시물 목록 테이블 -->
                <table class="table my-table-bordered">
                    <thead>
                    <tr class="text-center">
                        <th width="80">번호</th>
                        <th width="300">제목</th>
                        <th width="50">상태</th>
                        <th width="100">작성일</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    // 현재 페이지 설정 값
                    $currentPage = $_GET['page'] ?? 1;
                    $searchWord = $_GET['search'] ?? '';

                    // 페이지마다 보여줄 아이템 개수
                    $perPage = 10;
                    // 페이지의 시작 인덱스 값
                    $startIndex = ($currentPage - 1) * $perPage;

                    // 전체 게시글 수
                    $total = $post->countMine($requireUser['userIdx'], $searchWord);

                    // 전체 페이지 개수
                    $totalPage = ceil($total / $perPage);

                    // 현재 페이지에서 보여줄 마지막 페이지
                    $endPage = min($totalPage, $currentPage + 4);
                    $endPage = max(1, $endPage);

                    // 게시글 전체목록 가져오기
                    $posts = $post->getMyPostsByMyPage($requireUser['userIdx'], $searchWord, $startIndex, $perPage);

                    if ($posts) {
                        foreach ($posts as $postInfo) {

                            $postStatusColor = '#17a2b8';
                            $tableColor = '#fff';
                            $lockEmoji = '';
                            if ($postInfo['postStatus'] === '대기') {
                                $postStatusColor = '#ff9800';
                                $lockEmoji = '🔒';
                            } else if($postInfo['postStatus'] === '반려') {
                                $postStatusColor = '#dc3545';
                                $lockEmoji = '🔒';
                            } else if($postInfo['postStatus'] === '공지') {
                                $postStatusColor = '#6c757d';
                                $tableColor = 'gainsboro';
                                $lockEmoji = '🚨';
                            }

                            /// 30 글자 초과시 ... 저리
                            $title = $postInfo["title"];
                            if (strlen($title) > 30) {
                                // mb_substr: 한글이 깨지지 않도록 해줌
                                $title = str_replace($postInfo["title"], mb_substr($postInfo["title"], 0, 30, "utf-8") . "...", $postInfo["title"]);
                            }
                            ?>

                            <tr class="text-center" style="background-color: <?= $tableColor ?>">
                                <td><?= $postInfo['postIdx'] ?></td>
                                <td>
                                    <a href="/mk-board/post/read?postIdx=<?= $postInfo['postIdx'] ?>">
                                        <?php if ($postInfo['postStatus'] !== '승인') { ?>
                                            <span><?= $lockEmoji ?></span>
                                        <?php } ?>
                                        <?= htmlspecialchars($title) . " [" . $postInfo['comment_count'] . "]"; ?>
                                    </a>
                                </td>
                                <td><span class="badge badge-primary" style="background-color: <?= $postStatusColor ?>!important;"><?= $postInfo['postStatus'] ?></span></td>
                                <td><?= $postInfo['created_at'] ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>게시글이 없습니다.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <!-- 페이지네이션 -->
                <div class="d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" data-page="1" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php
                            for ($page = max($currentPage - 4, 1); $page <= $endPage; $page++) {
                                $isActive = $page == $currentPage ? 'active' : '';
                                echo "<li class='page-item $isActive' style='cursor: pointer'><span class='page-link' data-page='$page'>$page</span></li>";
                            }
                            ?>
                            <li class="page-item">
                                <a class="page-link" data-page="<?= $totalPage ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>