<!doctype html>
<?php
include __DIR__ . '/../part/head.php';
?>
<body>
<?php
include __DIR__ . '/../part/nav.php';
?>
<div class="m-5">
    <!--검색-->
    <div class="container mb-3">
        <div class="row justify-content-end">
            <div class="col-4">
                <!-- 검색 폼 -->
                <div class="d-flex justify-content-center">
                    <div class="form-inline row">
                        <div class="dropdown searchFiltering mr-2">
                            <small>종류: </small>
                            <button type="button" class="btn btn-secondary dropdown-toggle" id="selectedLogFilter" style="width: 120px" data-bs-toggle="dropdown" aria-expanded="false">
                                <?=!empty($_GET['filter']) ? $_GET['filter'] : "전체선택"?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="filterDropdown">
                                <!-- 드롭다운 메뉴 아이템들을 여기에 추가 -->
                                <a class="dropdown-item log-search-dropdown-item">전체선택</a>
                                <a class="dropdown-item log-search-dropdown-item">userName</a>
                                <a class="dropdown-item log-search-dropdown-item">targetClass</a>
                                <a class="dropdown-item log-search-dropdown-item">actionFunc</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="searchInput" class="sr-only">검색</label>
                            <input name="search" type="text" class="form-control" id="searchInput"
                                   placeholder="Search"
                                   value="<?= $_GET['search'] ?? '' ?>" disabled>
                            <input type="hidden" name="startDate" value="<?= $_GET['startDate'] ?? date('Y-m-d'); ?>">
                            <input type="hidden" name="endDate" value="<?= $_GET['endDate'] ?? date('Y-m-d'); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="input-group d-flex align-items-center">
                    <label class="mr-2">날짜:</label>
                    <input type="text" class="form-control date" id="startDate" style="cursor: pointer">

                    <label class="ml-2 mr-2">~</label>
                    <input type="text" class="form-control date" id="endDate" style="cursor: pointer">

                    <button id="dateSearchSubmit" type="button" class="btn btn-primary ml-2">완료</button>
                </div>
            </div>
        </div>
    </div>


    <div class="contianer-fluid">
        <!-- 로그 목록 테이블 -->
        <table class="table table-sm">
            <thead class="text-center">
            <tr>
                <th scope="col" style="width: 15%">날짜</th>
                <th scope="col" style="width: 15%">IP</th>
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
            // GET 매개변수에서 시작 날짜와 종료 날짜 가져오기
            $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');;
            $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');;
            $filter = !empty($_GET['filter']) ? $_GET['filter'] : "";
            $search = !empty($_GET['search']) ? $_GET['search'] : "";

            $startDate .= " 00:00:00";
            $endDate .= " 23:59:59";

            $logs = $log->getLogsByDateRange($filter, $search, $startDate, $endDate);
            if ($logs) {
                foreach ($logs as $logInfo) {
                    $detailBtn = '';
                    if(isset($logInfo['details'])) {
                        $detailBtn = '<button class="btn-sm btn-primary openDetailModal">detail</button>';
                    }
                    ?>

                    <tr style="height: 43px !important;" class="text-center logList">
                        <td style="display: none" class="logIdx"><?= $logInfo['logIdx'] ?></td>
                        <td><?= $logInfo['created_at'] ?></td>
                        <td><?= $logInfo['ip'] ?></td>
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
                                      rows="10" disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>