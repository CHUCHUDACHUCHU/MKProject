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
        <!-- 로그 목록 테이블 -->
        <table class="table table-sm">
            <thead class="text-center">
            <tr>
                <th scope="col" style="width: 15%">날짜</th>
                <th scope="col" style="width: 5%">userIdx</th>
                <th scope="col" style="width: 10%">userName</th>
                <th scope="col" style="width: 5%">targetIdx</th>
                <th scope="col" style="width: 20%">targetClass</th>
                <th scope="col" style="width: 20%">actionFunc</th>
                <th scope="col" style="width: 10%">updateStatus</th>
                <th scope="col" style="width: 5%">actionType</th>
                <th scope="col" style="width: 10%">details</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $logs = $log->getAllLog();
            if ($logs) {
                foreach ($logs as $logInfo) {
                    $detailBtn = '';
                    if(isset($logInfo['details'])) {
                        $detailBtn = '<button class="btn-sm btn-primary openDetailModal">detail</button>';
                    }
                    ?>

                    <tr class="text-center logList">
                        <td style="display: none" class="logIdx"><?= $logInfo['logIdx'] ?></td>
                        <td><?= $logInfo['created_at'] ?></td>
                        <td><?= $logInfo['userIdx'] ?></td>
                        <td><?= $logInfo['userName'] ?></td>
                        <td><?= $logInfo['targetIdx'] ?></td>
                        <td><?= $logInfo['targetClass'] ?></td>
                        <td><?= $logInfo['actionFunc'] ?></td>
                        <td><?= $logInfo['updateStatus'] ?></td>
                        <td><?= $logInfo['actionType'] ?></td>
                        <td><?= $detailBtn ?></td>
                        <td style="display: none" class="details"><?= $logInfo['details'] ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>생성된 로그가 없습니다.</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModal"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">DETAIL</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="detailModalContent" class="form-label">내용:</label>
                            <textarea class="form-control details" name="details"
                                      rows="3" disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>