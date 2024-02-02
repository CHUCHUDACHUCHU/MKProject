<!DOCTYPE html>
<?php
include __DIR__ . '/../part/head.php';
?>
<body>
<?php
include __DIR__ . '/../part/nav.php';
?>
<div class="container mt-4">
    <!-- 검색 -->
    <div class="row justify-content-center">
        <div class="search-container col-md-4">
            <form action="" method="get">
                <div class="row">
                    <div class="col-md-8">
                        <label for="searchInput" class="sr-only">검색</label>
                        <input name="search" type="text" class="form-control w-100" id="searchInput"
                               placeholder="Search" value="<?= $_GET['search'] ?? '' ?>">
                    </div>
                    <div class="col-md-3" style="padding: 0px">
                        <button id="searchSubmit" type="submit" class="btn btn-primary mb-2">검색</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 필터링 및 대량 처리 -->
        <div class="row justify-content-center mt-3" style="margin-left: 20px">
            <div class="col-md-0">
                <!-- 전체 클릭할 수 있는 체크박스 -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll"></label>
                </div>
            </div>
            <div class="col ml-auto">
                <!-- 승인, 반려, 대기 버튼 -->
                <div>
                    <a href="" style="color: black; font-weight: bolder">승인</a>
                    <a href="" style="color: black; font-weight: bolder">반려</a>
                    <a href="" style="color: black; font-weight: bolder">대기</a>
                </div>
            </div>
            <div class="col-md-4">
                <!-- 필터링 드롭다운 버튼 -->
                <div class="row">
                    <div class="dropdown mr-4 statusFiltering">
                        <small>상태: </small>
                        <button type="button" class="btn btn-secondary dropdown-toggle" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                            $statusText = isset($_GET['postStatus']) ? $_GET['postStatus'] : '전체선택';
                            ?>
                            <span><?= $statusText ?></span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdown">
                            <!-- 드롭다운 메뉴 아이템들을 여기에 추가 -->
                            <a class="dropdown-item" href="/mk-board/post/manage">전체선택</a>
                            <a class="dropdown-item" href="/mk-board/post/manage?postStatus=승인">승인</a>
                            <a class="dropdown-item" href="/mk-board/post/manage?postStatus=반려">반려</a>
                            <a class="dropdown-item" href="/mk-board/post/manage?postStatus=대기">대기</a>
                        </div>
                    </div>
                    <div class="dropdown departMentFiltering">
                        <small>부서: </small>
                        <button type="button" class="btn btn-secondary dropdown-toggle" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                            $departmentText = isset($_GET['departmentName']) ? $_GET['departmentName'] : '전체선택';
                            ?>
                            <span><?= $departmentText ?></span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdown">
                            <!-- 드롭다운 메뉴 아이템들을 여기에 추가 -->
                            <a class="dropdown-item" href="/mk-board/post/manage">전체선택</a>
                            <?php foreach ($departments as $dept): ?>
                                <a class="dropdown-item" href="/mk-board/post/manage?departmentName=<?= $dept['departmentName'] ?>"><?= $dept['departmentName'] ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<hr/>
    <div id="loading-spinner" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 1000;">
        <img src="/mk-board/assets/img/spinner.gif" alt="로딩 중..." /> <!-- 스피너 이미지 등을 넣어주세요 -->
        <b style="display: block">게시글 권한 변경 중...</b>
    </div>
    <div>
        <!-- 회원 리스트 -->
        <?php
        // 현재 페이지 설정 값
        $currentPage = $_GET['page'] ?? 1;
        $searchWord = $_GET['search'] ?? '';
        $postStatus = $_GET['postStatus'] ?? '';
        $departmentName = $_GET['departmentName'] ?? '';

        // 페이지마다 보여줄 아이템 개수
        $perPage = 15;
        // 페이지의 시작 인덱스 값
        $startIndex = ($currentPage - 1) * $perPage;

        $total = $post->countManagePosts($searchWord, $postStatus, $departmentName);

        // 전체 페이지 개수
        $totalPage = ceil($total / $perPage);

        // 현재 페이지에서 보여줄 마지막 페이지
        $endPage = min($totalPage, $currentPage + 4);
        $endPage = max(1, $endPage);

        // 게시글 전체목록 가져오기
        $posts = $post->getManagePosts($searchWord, $startIndex, $perPage, $postStatus, $departmentName);

        if($posts) {
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
                <!-- 게시글 리스트 정보 -->
                <div class="card mb-2 postInfoDashboard">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-10">
                            <div class="row" style="margin-left: 5px">
                                <!-- 체크박스 -->
                                <div class="col-md-1" style="display:none;">
                                    <p class="postIdx" id="postIdx"><?= $postInfo['postIdx'] ?></p>
                                </div>

                                <!-- 체크박스 -->
                                <div class="col-md-1">
                                    <input type="checkbox" name="your_checkbox_name" id="your_checkbox_id">
                                    <label for="your_checkbox_id"></label>
                                </div>

                                <!-- 작성자 -->
                                <div class="col-md-1">
                                    <div class="row">
                                        <a href="/mk-board/user/read?userIdx=<?= $postInfo['userIdx'] ?>" class="card-title" style="color: black; font-weight: bolder; cursor: pointer"><?= $postInfo['userName'] ?></a>
                                    </div>
                                </div>

                                <!-- 부서 -->
                                <div class="col-md-1">
                                    <p class="card-text" style="font-size: small; font-weight: bolder"><?= $postInfo['departmentName'] ?></p>
                                </div>

                                <!-- 제목 -->
                                <div class="col-md-8 ml-5">
                                    <a href="/mk-board/post/read?postIdx=<?= $postInfo['postIdx'] ?>">
                                        <?php if ($postInfo['postStatus'] !== '승인') { ?>
                                            <span><?= $lockEmoji ?></span>
                                        <?php } ?>
                                        <?= htmlspecialchars($title) . " [" . $postInfo['comment_count'] . "]"; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 row">
                            <div class="d-flex align-items-center">
                                <div aria-label="Button group with nested dropdown" class="d-flex align-items-center postStatusBox">
                                    <button type="button" class="btn btn-primary dropdown-toggle" style="background-color: <?=$postStatusColor?> !important; height: 30px; line-height: 15px;" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?= $postInfo['postStatus'] ?>
                                    </button>
                                    <input type="hidden" class="postIdx" name="postIdx" value="<?= $postInfo['postIdx'] ?>">
                                    <ul class="dropdown-menu" style="cursor: pointer">
                                        <li><a class="dropdown-item post-status-dropdown-item" data-value="승인">승인</a></li>
                                        <li><a class="dropdown-item post-status-dropdown-item" data-value="대기">대기</a></li>
                                        <li><a class="dropdown-item openRejectMessageModal" data-value="반려">반려</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>게시글이 없습니다.</td></tr>";
        }

        ?>
    </div>
    
    <div class="modal fade" id="rejectMessageModal" tabindex="-1" aria-labelledby="rejectMessageModal"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="modal-loading-spinner" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 1000;">
                    <img src="/mk-board/assets/img/spinner.gif" alt="로딩 중..." /> <!-- 스피너 이미지 등을 넣어주세요 -->
                    <b style="display: block">게시글 권한 변경 중...</b>
                </div>
                <form action="/mk-board/comment/create" method="post" class="rejectMessageModalForm" id="rejectMessageModalForm">
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
</body>
</html>
