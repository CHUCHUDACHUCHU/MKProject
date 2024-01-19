<!DOCTYPE html>
<?php
use Model\Post;
$post = new Post();
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
            <div class="mb-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#emailChangeModal"><span>이메일변경</span></button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passwordChangeModal"><span>비밀번호변경</span></button>
            </div>
            <form action="/mk-board/user/update/all" method="post">
                <div class="form-group mb-1">
                    <label class="mb-1" style="font-weight: bold">이름</label>
                    <input type="text" class="form-control mb-1"  value="<?=$nowUser['userName']?>" name="userName">
                </div>
                <div class="form-group mb-1">
                    <label class="mb-1" style="font-weight: bold">이메일</label>
                    <input type="text" class="form-control mb-1" value="<?=$nowUser['userEmail']?>" name="userEmail" readonly >
                </div>
                <div class="form-group mb-1">
                    <label class="mb-1" style="font-weight: bold">권한</label>
                    <input type="text" class="form-control mb-1" value="<?=$nowUser['userStatus']?>" name="userPhone" readonly>
                </div>
                <div class="form-group mb-1">
                    <label class="mb-1" style="font-weight: bold">부서</label>
                    <input type="text" class="form-control mb-1" value="<?=$nowUser['userDepart']?>" name="userDepart">
                </div>
                <div class="form-group mb-1">
                    <label class="mb-1" style="font-weight: bold">휴대폰</label>
                    <input type="text" class="form-control mb-1" value="<?=$nowUser['userPhone']?>" name="userPhone" placeholder="010-0000-0000">
                </div>
                <div class="btn-group mb-2" role="group" aria-label="Basic example">
                    <button type="submit" class="btn btn-primary">수정</button>
                </div>
            </form>

            <div class="modal fade" id="emailChangeModal" aria-hidden="true" aria-labelledby="emailChangeModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="emailChangeModalToggleLabel">이메일 변경</h1>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="mb-5">
                                    <li><b>변경할 이메일을 입력하세요.</b></li>
                                </div>
                                <div class="mb-3 d-flex">
                                    <input type="email" class="form-control" id="email" >
                                    <input class="btn btn-primary verificationCodeSendBtn" type="button" value="인증번호 발송">
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <input type="text" class="form-control col" id="codeInputBox">
                                        <input class="btn btn-primary codeInputBoxBtn col-2" id="codeInputBoxBtn" type="button" value="확인">
                                        <span class="col" id="codeSessionLiveTime"></span>
                                    </div>
                                </div>
                                <span id = "codeCheckMessage" style="display: block; font-size: 10px"></span>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary emailUpdateBtn" id="emailUpdateBtn">수정</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="passwordChangeModal" aria-labelledby="pwChangeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="pwChangeModalLabel">비밀번호 변경</h1>
                    </div>
                    <div class="modal-body">
                        <form action="/mk-board/user/update/password" method="post">
                            <div class="mb-3">
                                <label for="nowPassword" class="col-form-label">현재 비밀번호:</label>
                                <input type="password" class="form-control" id="nowPassword" name="nowPassword">
                            </div>
                            <div class="mb-3">
                                <label for="changePassword" class="col-form-label">새 비밀번호:</label>
                                <span id = "password-check-message" style="display: block; margin-left: 5px; font-size: 10px"></span>
                                <input type="password" class="form-control" id="changePassword" name="changePassword" placeholder="Password">
                            </div>
                            <div class="mb-3">
                                <label for="changePasswordCheck" class="col-form-label">비밀번호 확인:</label>
                                <span id = "password-match-message" style="display: block; margin-left: 5px; font-size: 10px"></span>
                                <input type="password" class="form-control" id="changePasswordCheck" name="changePasswordCheck" placeholder="Password Check">
                            </div>
                            <button type="submit" class="btn btn-primary" id="changePasswordBtn" style="display: none">변경</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
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
                <table class="table table-bordered">
                    <thead>
                    <tr class="text-center">
                        <th width="80">번호</th>
                        <th width="300">제목</th>
                        <th width="50">조회수</th>
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
                    $total = $post->countMine($nowUser['userIdx'], $searchWord);

                    // 전체 페이지 개수
                    $totalPage = ceil($total / $perPage);

                    // 현재 페이지에서 보여줄 마지막 페이지
                    $endPage = $totalPage > $currentPage + 4 ? $currentPage + 4 : $totalPage;
                    $endPage = $endPage < 10 && $totalPage > 10 ? 10 : $endPage;

                    // 게시글 전체목록 가져오기
                    $posts = $post->getMyPosts($nowUser['userIdx'], $searchWord, $startIndex, $perPage);

                    if ($posts) {
                        foreach ($posts as $postInfo) {
                            /// 30 글자 초과시 ... 저리
                            $title = $postInfo["title"];
                            if (strlen($title) > 30) {
                                // mb_substr: 한글이 깨지지 않도록 해줌
                                $title = str_replace($postInfo["title"], mb_substr($postInfo["title"], 0, 30, "utf-8") . "...", $postInfo["title"]);
                            }
                            ?>

                            <tr class="text-center">
                                <td><?= $postInfo['postIdx'] ?></td>
                                <td>
                                    <a href="/mk-board/post/read?postIdx=<?= $postInfo['postIdx'] ?>">
                                        <?= htmlspecialchars($title) . " [" . $postInfo['comment_count'] . "]"; ?>
                                        <?php if ($postInfo['is_new']) { ?>
                                            <span class="badge badge-primary">new</span>
                                        <?php } ?>
                                    </a>
                                </td>
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
                                echo "<li class='page-item $isActive'><span class='page-link' data-page='$page'>$page</span></li>";
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