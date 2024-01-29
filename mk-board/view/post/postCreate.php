<!doctype html>
<?php
include __DIR__ . '/../part/head.php';
?>
<body>
    <?php
    include __DIR__ . '/../part/nav.php';
    $radioAccess = 'none';
    $radioValue = '대기';
    if($nowUser['userStatus'] === '관리자') {
        $radioAccess = 'block';
        $radioValue = '승인';
    }
    ?>
    <div class="container mt-5">
        <h3 class="d-inline" style="font-weight: bolder">게시물 작성</h3>
        <hr/>


        <form action="/mk-board/post/create/form" method="post" class="myPostCreateFormGroup" id="myPostCreateFormGroup">
            <div class="form-group">
                <input type="text" class="form-control mb-2" id="title" name="title" placeholder="제목을 입력하세요">
                <input type="text" class="form-control" value="<?=$nowUser['userEmail']?>" readonly>
            </div>

            <div class="custom-control custom-radio" style="display: <?= $radioAccess ?>">
                <input type="radio" id="commonRadio" name="commonOrNotifyRadio" class="custom-control-input" value="<?= $radioValue ?>" checked>
                <label class="custom-control-label" for="commonRadio">일반 게시글</label>
            </div>
            <div class="custom-control custom-radio" style="display: <?= $radioAccess ?>">
                <input type="radio" id="notifyRadio" name="commonOrNotifyRadio" class="custom-control-input" value="공지">
                <label class="custom-control-label" for="notifyRadio" style="color: #dc3545;">공지 게시글</label>
            </div>
            <br/>

<!--            드롭존-->
            <div class="dropzone"></div>

            <!-- 포스팅 - 이미지/동영상 dropzone 영역 -->
            <ul class="list-unstyled mb-0" id="dropzone-preview">
                <li class="mt-2" id="dropzone-preview-list">
                    <!-- This is used as the file preview template -->
                    <div class="border rounded-3">
                        <div class="d-flex align-items-center p-2">
                            <div class="flex-shrink-0 me-3" style="margin-right: 10px">
                                <div class="width-8 h-auto rounded-3">
                                    <img data-dz-thumbnail="data-dz-thumbnail" class="w-full h-auto rounded-3 block" src="#" style="width: 120px;"/>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="pt-1">
                                    <h6 class="font-semibold mb-1" data-dz-name="data-dz-name">&nbsp;</h6>
                                    <p class="text-sm text-muted fw-normal" data-dz-size="data-dz-size"></p>
                                    <strong class="error text-danger" data-dz-errormessage="data-dz-errormessage"></strong>
                                </div>
                            </div>
                            <div class="shrink-0 ms-3">
                                <button data-dz-remove="data-dz-remove" class="btn btn-sm btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>



            <div class="form-group">
                <hr/>
                <textarea class="form-control" id="content" name="content" rows="10" placeholder="내용을 입력하세요"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" id="completeButton">완료</button>
        </form>
    </div>
</body>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<script>
    Dropzone.autoDiscover = false;
    const dropzonePreviewNode = document.querySelector('#dropzone-preview-list');
    dropzonePreviewNode.id = '';
    const previewTemplate = dropzonePreviewNode.parentNode.innerHTML;
    dropzonePreviewNode.parentNode.removeChild(dropzonePreviewNode);
    const maxFilesAllowed = 5;
    const maxFileSize = 100;

    const dropzone = new Dropzone(".dropzone", {
        url: "http://localhost/mk-board/file/upload", // 파일을 업로드할 서버 주소 url.
        method: "post",

        previewTemplate: previewTemplate,                       // 커스텀 업로드 테마
        previewsContainer: '#dropzone-preview',                 // 미리보기 드롭존 생성
        dictDefaultMessage: '+ 파일을 마우스로 끌어 오세요',        //초기 inner text 변경
        autoProcessQueue: false,                                //수동으로 업로드
        maxFiles: maxFilesAllowed,                                            //최대 갯수
        parallelUploads: maxFilesAllowed,                                     //동시 최대 갯수
        maxFilesize: maxFileSize,                                        // 최대업로드용량 : 100MB
        acceptedFiles:  'image/*, ' +
                        'application/pdf, ' +
                        'application/msword, ' +
                        'application/vnd.ms-excel, ' +
                        'application/vnd.ms-powerpoint, ' +
                        'text/plain, ' +
                        'application/zip',
        dictMaxFilesExceeded: `파일 갯수가 너무 많습니다. 최대${maxFilesAllowed}개 입니다.`,
        dictFileTooBig: `${maxFileSize}MB를 초과합니다. 파일을 삭제해주세요.`

    });

    let uploadedFileIndexes = [];

    const completeButton = document.getElementById('completeButton');
    completeButton.addEventListener('click', function (event) {
        event.preventDefault();
        const fileCount = dropzone.files.length;
        const isAllFilesValid = dropzone.files.every(function (file) {
            return file.size <= maxFileSize * 1024 * 1024;
        });

        if (fileCount <= maxFilesAllowed && isAllFilesValid) {
            if(fileCount === 0) {
                // 파일 없을 시 바로 form 제출
                document.getElementById('myPostCreateFormGroup').submit();
            }
            dropzone.processQueue();
        } else {
            if (!isAllFilesValid) {
                alert(`파일은 최대 ${maxFileSize}MB 이하만 허용됩니다. 100MB를 넘는 파일이 존재합니다.`);
            } else {
                alert(`파일은 최대 ${maxFilesAllowed}개 이하만 허용됩니다.`);
            }
        }
    });

    // 파일 업로드 성공 시
    dropzone.on('success', function (file, data) {
        // 파일 업로드 성공 후의 로직을 여기에 추가
        console.log('파일 업로드 중 : ', data.result.fileOriginName);

        fetch('/mk-board/file/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                fileName: data.result.fileName,
                fileOriginName: data.result.fileOriginName,
                fileSize: data.result.fileSize
            }),
        })
            .then((res) => {
                if (res.status !== 200) {
                    throw new Error('네트워크 응답이 200이 아닙니다.');
                }
                return res.json();
            })
            .then((data) => {
                if (data.result.status === 'success') {
                    uploadedFileIndexes.push(data.result.fileIdx);
                    console.log('전체 파일 개수:', dropzone.files.length);
                    console.log('업로드한 파일 개수:', uploadedFileIndexes.length);

                    if (uploadedFileIndexes.length === dropzone.files.length) {
                        savePostToDB();
                    }
                } else {
                    alert(data.result.message);
                }
            })
            .catch((err) => {
                alert('파일 DB 저장 요청 : fetch 에러 ' + err);
            })
    });

    // 게시글 DB 저장 함수
    function savePostToDB() {
        const title = document.getElementById('title');
        const content = document.getElementById('content');
        const radioButtons = document.getElementsByName("commonOrNotifyRadio");
        let selectedValue = null;
        radioButtons.forEach(function (radioButton) {
            if (radioButton.checked) {
                selectedValue = radioButton;
            }
        });

        console.log('게시글 DB 저장 요청');
        fetch('/mk-board/post/create/fetch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                title: title.value,
                content: content.value,
                commonOrNotifyRadio: selectedValue.value
            }),
        })
            .then((res) => {
                if (res.status !== 200) {
                    throw new Error('Network response was not 200');
                }
                return res.json();
            })
            .then((data) => {
                if(data.result.status === 'success') {
                    console.log('게시글 DB저장 성공!');
                    fileUpdatePostIdx(data.result.postIdx);
                } else {
                    alert(data.result.message);
                }
            })
            .catch((err) => {
                alert('게시글 DB 저장 요청 : fetch 에러 ' + err);
            })
    }

    function fileUpdatePostIdx(postIdx) {
        fetch('/mk-board/file/update/postIdx', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                uploadedFileIndexes: uploadedFileIndexes,
                postIdx: postIdx
            }),
        })
            .then(response => response.json())
            .then(data => {
                if(data.result.status === 'success') {
                    console.log('파일과 게시글 연결 성공!');
                    location.href=`/mk-board/post/read?postIdx=${postIdx}`;
                } else {
                    alert(data.result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>
</html>