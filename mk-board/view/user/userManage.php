<!DOCTYPE html>
<?php
include __DIR__ . '/../part/head.php';
?>
<body>
<?php
include __DIR__ . '/../part/nav.php';
?>
<!-- Main Content -->
<div class="container mt-4">
    <div class="row">
        <!--left-->
        <div class="col-8">
            <!-- 검색 섹션 -->
            <div class="container text-center">
                <form action="" method="get">
                    <div class="row">
                        <div class="">
                            <label for="searchInput" class="sr-only">검색</label>
                            <input name="search" type="text" class="form-control w-100" id="searchInput"
                                   placeholder="Search"
                                   value="<?= $_GET['search'] ?? '' ?>">
                        </div>
                        <div class="">
                            <button id="searchSubmit" type="submit" class="btn btn-primary mb-2">검색</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- 회원 리스트 -->

            <?php
            // 현재 페이지 설정 값
            $currentPage = $_GET['page'] ?? 1;
            $searchWord = $_GET['search'] ?? '';

            // 페이지마다 보여줄 아이템 개수
            $perPage = 10;
            // 페이지의 시작 인덱스 값
            $startIndex = ($currentPage - 1) * $perPage;

            // 전체 게시글 수
            $total = $user->countAll($searchWord);

            // 전체 페이지 개수
            $totalPage = ceil($total / $perPage);

            // 현재 페이지에서 보여줄 마지막 페이지
            $endPage = $totalPage > $currentPage + 4 ? $currentPage + 4 : $totalPage;
            $endPage = $endPage < 10 && $totalPage > 10 ? 10 : $endPage;

            // 사용자 전체목록 가져오기
            $users = $user->getAllUsers($searchWord, $startIndex, $perPage);

            if($users) {
                foreach ($users as $userInfo) {
                    $userStatusColor = '#ff9800';
                    if ($userInfo['userStatus'] === '관리자') {
                        $userStatusColor = 'mediumseagreen';
                    } else if ($userInfo['userStatus'] === '정지') {
                        $userStatusColor = '#dc3545';
                    }
                    ?>
                    <!-- 회원 정보 -->
                    <div class="card mb-3 userInfoDashboard">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-1">
                                <img src="/mk-board/assets/img/logo.png" width="40px" height="40px">
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <a href="/mk-board/user/read?userIdx=<?= $userInfo['userIdx'] ?>" class="card-title" style="font-weight: bolder; cursor: pointer"><?= $userInfo['userName'] ?></a>
                                    <p class="card-text userEmail" style="font-weight: bold">&nbsp @ <?= $userInfo['userEmail'] ?></p>
                                </div>
                            </div>
                            <div class="col-md-3 row justify-content-end">
                                <div aria-label="Button group with nested dropdown" class="d-flex">
                                    <button type="button" class="btn btn-primary dropdown-toggle" style="background-color: <?=$userStatusColor?> !important; width: 90px" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?= $userInfo['userStatus'] ?>
                                    </button>
                                    <ul class="dropdown-menu" style="cursor: pointer">
                                        <li><a class="dropdown-item" data-value="관리자">관리자</a></li>
                                        <li><a class="dropdown-item" data-value="일반">일반</a></li>
                                        <li><a class="dropdown-item" data-value="정지">정지</a></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-danger ml-auto userDeleteBtn">삭제</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>회원이 없습니다.</td></tr>";
            }

            ?>
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

        <!--right-->
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">계정생성</h2>
                </div>
                <div class="card-body">
                    <!-- 로딩 스피너 추가 -->
                    <div id="loading-spinner" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <img src="/mk-board/assets/img/spinner.gif" alt="로딩 중..." /> <!-- 스피너 이미지 등을 넣어주세요 -->
                    </div>
                    <form action="/mk-board/user/create" method="post" id="userCreationForm">
                        <div class="form-group">
                            <label>이름</label>
                            <input type="text" class="form-control" name="userName" placeholder="Enter Name">
                        </div>
                        <div class="form-group">
                            <label>이메일</label>
                            <input type="text" class="form-control" name="userEmail" placeholder="Enter Email">
                        </div>
                        <div class="form-group">
                            <label>휴대폰</label>
                            <input type="text" class="form-control" name="userPhone" placeholder="Enter Phone Number">
                        </div>
                        <div class="form-group">
                            <label>소속</label>
                            <div class="input-group">
                                <select class="form-select form-control" id="inputGroupSelect01" name="departmentIdx">
                                    <option selected>소속 선택</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['departmentIdx'] ?>"><?= $dept['departmentName'] ?></option>
                                    <?php endforeach; ?>
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>권한</label>
                            <div class="input-group">
                                <select class="form-select form-control" id="inputGroupSelect01" name="userStatus">
                                    <option selected>Choose...</option>
                                    <option value="일반">일반</option>
                                    <option value="관리자">관리자</option>
                                    <option value="중지">중지</option>
                                </select>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary float-right"
                                style="background-color: dodgerblue !important;">사용자 생성</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
