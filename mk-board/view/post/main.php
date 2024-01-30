<!doctype html>
<?php
include __DIR__ . '/../part/head.php';
?>
<body>
    <?php
    include __DIR__ . '/../part/nav.php';
    ?>
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
                    <div class="form-inline full-width-form" style="margin-left: 150px">
                        <div class="form-group flex-fill">
                            <label for="searchInput" class="sr-only">검색</label>
                            <input name="search" type="text" class="form-control w-100" id="searchInput"
                                   placeholder="Search"
                                   value="<?= $_GET['search'] ?? '' ?>">
                        </div>
                        <button id="searchSubmit" type="submit" class="btn btn-primary">검색</button>
                    </div>

                    <div class="ml-auto">
                        <div class="row">
                            <!-- 필터링 드롭다운 버튼 -->
                            <div class="dropdown departmentFiltering ml-2">
                                <small>부서: </small>
                                <button type="button" class="btn btn-secondary dropdown-toggle" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php
                                    $departmentText = isset($_GET['departmentName']) ? $_GET['departmentName'] : '전체선택';
                                    ?>
                                    <span><?= $departmentText ?></span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="filterDropdown">
                                    <!-- 드롭다운 메뉴 아이템들을 여기에 추가 -->
                                    <a class="dropdown-item" href="/mk-board/post/list">전체선택</a>
                                    <?php foreach ($departments as $dept): ?>
                                        <a class="dropdown-item" href="/mk-board/post/list?departmentName=<?= $dept['departmentName'] ?>"><?= $dept['departmentName'] ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div class="contianer-fluid">
            <!-- 게시물 목록 테이블 -->
            <table class="table table-bordered">
                <thead>
                <tr class="text-center">
                    <th width="30">번호</th>
                    <th width="200">제목</th>
                    <th width="50">이름</th>
                    <th width="50">부서</th>
                    <th width="30">상태</th>
                    <th width="30">조회수</th>
                    <th width="100">작성일</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // 현재 페이지 설정 값
                $currentPage = $_GET['page'] ?? 1;
                $searchWord = $_GET['search'] ?? '';
                $departmentName = $_GET['departmentName'] ?? '';

                // 페이지마다 보여줄 아이템 개수
                $perPage = 20;
                // 페이지의 시작 인덱스 값
                $startIndex = ($currentPage - 1) * $perPage;

                $notify = $post->getNotifyAll();
                $total = $post->countNotifyAll();

                // 게시글 전체목록 가져오기
                if($nowUser['userStatus'] === '관리자') {
                    $total += $post->countMainPostsByAdmin($searchWord, $departmentName);
                } else {
                    $total += $post->countMainPostsByCommon($searchWord, $nowUser['userIdx'], $departmentName);
                }

                // 전체 페이지 개수
                $totalPage = ceil($total / $perPage);

                // 현재 페이지에서 보여줄 마지막 페이지
                $endPage = min($totalPage, $currentPage + 4);
                $endPage = max(1, $endPage);

                // 게시글 전체목록 가져오기
                if($nowUser['userStatus'] === '관리자') {
                    $posts = $post->getMainPostsByAdmin($searchWord, $startIndex, $perPage, $departmentName);
                } else {
                    $posts = $post->getMainPostsByCommon($searchWord, $nowUser['userIdx'], $startIndex, $perPage);
                }

                //일단 공지글 먼저 조회!
                if ($notify) {
                    foreach ($notify as $notifyInfo) {
                        $postStatusColor = '#6c757d';
                        $tableColor = 'gainsboro';
                        $lockEmoji = '🚨';


                        /// 30 글자 초과시 ... 저리
                        $title = $notifyInfo["title"];
                        if (strlen($title) > 50) {
                            // mb_substr: 한글이 깨지지 않도록 해줌
                            $title = str_replace($notifyInfo["title"], mb_substr($notifyInfo["title"], 0, 30, "utf-8") . "...", $notifyInfo["title"]);
                        }
                        ?>

                        <tr class="text-center" style="background-color: <?= $tableColor ?>">
                            <td><?= $notifyInfo['postIdx'] ?></td>
                            <td>
                                <a href="/mk-board/post/read?postIdx=<?= $notifyInfo['postIdx'] ?>">
                                    <span><?= $lockEmoji ?></span>
                                    <?= htmlspecialchars($title) . " [" . $notifyInfo['comment_count'] . "]"; ?>
                                </a>
                            </td>
                            <td>
                                <a href="/mk-board/user/read?userIdx=<?= $notifyInfo['userIdx'] ?>" class="card-title" style="color: black; font-weight: bolder; cursor: pointer"><?= $notifyInfo['userName'] ?></a>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">👑</span>
                            </td>
                            <td><?= $notifyInfo['departmentName'] ?></td>
                            <td><span class="badge badge-primary" style="background-color: <?= $postStatusColor ?>!important;"><?= $notifyInfo['postStatus'] ?></span></td>
                            <td><?= $notifyInfo['views'] ?></td>
                            <td><?= $notifyInfo['created_at'] ?></td>
                        </tr>
                    <?php
                    }
                }

                //실제 게시글 조회!!!
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
                        }


                        /// 30 글자 초과시 ... 저리
                        $title = $postInfo["title"];
                        if (strlen($title) > 50) {
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
                            <td>
                                <a href="/mk-board/user/read?userIdx=<?= $postInfo['userIdx'] ?>" class="card-title" style="color: black; font-weight: bolder; cursor: pointer"><?= $postInfo['userName'] ?></a>
                                <?php if ($postInfo['userStatus'] === '관리자') { ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">👑</span>
                                <?php } ?>
                            </td>
                            <td><?= $postInfo['departmentName'] ?></td>
                            <td><span class="badge badge-primary" style="background-color: <?= $postStatusColor ?>!important;"><?= $postInfo['postStatus'] ?></span></td>
                            <td><?= $postInfo['views'] ?></td>
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
</body>
</html>