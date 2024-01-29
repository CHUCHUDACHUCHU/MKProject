document.addEventListener('DOMContentLoaded', function (event) {

    /**
     * 네이게이션바 로그인세션타임
     * */
    const startInterval = (seconds, callback) => {
        callback();
        return setInterval(callback, seconds * 1000);
    };

    //로그인 인증 세션타임
    let loginRemainingTime = 1800;
    const intervalLoginSessionTime = startInterval(1, function () {
        // 이 부분에서 요소를 찾을 수 있도록 실행 시점을 보장합니다.
        const sessionTimeElement = document.getElementById('sessionTime');
        if (sessionTimeElement) {
            sessionTimeElement.textContent = secToTime(loginRemainingTime);
            loginRemainingTime--;

            if(loginRemainingTime < 0) {
                clearInterval(intervalLoginSessionTime);
                location.href='/mk-board/auth/sessionout'
            }
        }
    });

    //시분초 변환 함수
    function secToTime(duration) {
        let seconds = Math.floor(duration % 60),
            minutes = Math.floor((duration / 60) % 60),
            hours = Math.floor((duration / (60 * 60)) % 24);

        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        seconds = (seconds < 10) ? "0" + seconds : seconds;

        return minutes + ":" + seconds;
    }
    
    /**
     * 로딩창 스피너 이벤트 등록!
     * */

    const userCreationForm = document.getElementById('userCreationForm');
    const loadingSpinner = document.getElementById('loading-spinner');

    if(userCreationForm) {
        userCreationForm.addEventListener('submit', function () {
            loadingSpinner.style.display = 'block';
            return true;
        })
    }


    /**
     * 네비게이션바 클릭 액티브
     * */
    const currentPath = window.location.pathname;

    if (currentPath === "/mk-board/post/list") {
        document.getElementById("homeNav").classList.add("active");
    } else if (currentPath === "/mk-board/user/my-page") { // Adjust the path for MyPage
        document.getElementById("myPageNav").classList.add("active");
    } else if (currentPath === '/mk-board/user/manage') {
        document.getElementById("manageUserNav").classList.add("active");
    } else if (currentPath === '/mk-board/post/manage') {
        document.getElementById("managePostNav").classList.add("active");
    }


    /**
     * 인증번호 이메일 발송 이벤트처리
     * */
    const verificationCodeSendBtn = document.querySelector('.verificationCodeSendBtn');
    if(verificationCodeSendBtn) {
        verificationCodeSendBtn.addEventListener('click', function () {
            const expEmailText = /^[A-Za-z0-9\.\-]+@[A-Za-z0-9\.\-]+\.[A-Za-z0-9\.\-]+$/;
            const email = document.getElementById('email');
            if (codeCheck === 1) {
                alert('인증이 완료되었습니다.');
                return false;
            }

            if (email.value == '') {
                alert("이메일을 입력해주세요.");
                email.focus();
                return false;
            }
            if (!expEmailText.test(email.value)) {
                alert('이메일 형식이 올바르지 않습니다.');
                email.focus();
                return false;
            }

            fetch(`/mk-board/user/code/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    userEmail: email.value,
                })
            })
                .then((res) => {
                    if (res.status !== 200) {
                        throw new Error('Network response was not 200');
                    }
                    return res.json();
                })
                .then((data) => {
                    if (data.result.status === 'success') {
                        //인증번호 세션타임
                        let authCertSessionTime = 180;
                        const intervalCodeSessionTime = startInterval(1, function () {
                            const codeSessionLiveTime = document.getElementById('codeSessionLiveTime');
                            if (codeSessionLiveTime) {
                                codeSessionLiveTime.textContent = secToTime(authCertSessionTime);
                                authCertSessionTime--;

                                if (authCertSessionTime < 0 || codeCheck == 1) {
                                    clearInterval(intervalCodeSessionTime);
                                    fetch(`/mk-board/user/code/sessionout`)
                                        .catch((err) => {
                                            alert('인증번호 세션 만료 요청 : fetch 에러' + err);
                                        });
                                }
                            }
                        })
                        fetch(`/mk-board/user/code/send`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                email: email.value,
                            }),
                        })
                            .then((res) => {
                                if (res.status !== 200) {
                                    throw new Error('Network response was not 200');
                                }
                                return res.json();
                            })
                            .then((data) => {
                                if (data.result.status !== 'success') {
                                    alert(data.result.message);
                                }
                            })
                    } else {
                        alert(data.result.message);
                    }
                })
                .catch((err) => {
                    alert(err);
                })
        });
    }


    /**
     * 인증번호 확인 이벤트처리
     * */
    let codeCheck = 0;
    const result = document.getElementById('codeCheckMessage');
    const codeInputBoxBtn = document.querySelector('.codeInputBoxBtn');
    if(codeInputBoxBtn) {
        codeInputBoxBtn.addEventListener('click', function () {
            const codeInputBox = document.getElementById('codeInputBox');
            fetch(`/mk-board/user/code/check`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: codeInputBox.value,
                }),
            })
                .then((res) => {
                    if(res.status != 200) {
                        throw new Error('Network response was not 200');
                    }
                    return res.json();
                })
                .then((data) => {
                    if (data.result.status === "success") {
                        codeCheck = 1;
                        result.style.display = "block";
                        result.style.color = "green";
                        result.innerHTML = `&nbsp;&nbsp;&nbsp;${data.result.message}`;
                    } else if(data.result.status === 'fail') {
                        result.style.display = "block";
                        result.style.color = "red";
                        result.innerHTML = `&nbsp;&nbsp;&nbsp;${data.result.message}`;
                        codeInputBox.focus();
                        codeInputBox.addEventListener("keydown", function () {
                            result.style.display = "none";
                        });
                    } else {
                        alert(data.result.message)
                    }})
                .catch((err) => {
                    alert('인증번호 확인 요청 : fetch 에러 ' + err);
                })
        })
    }


    /**
     * 이메일 수정 버튼 이벤트 처리
     * */
    const emailUpdateBtn = document.querySelector('.emailUpdateBtn');
    if(emailUpdateBtn) {
        emailUpdateBtn.addEventListener('click', function () {
            if(codeCheck == 0) {
                alert('인증번호를 확인해주세요.')
            } else {
                const email = document.getElementById('email');

                fetch(`/mk-board/user/update/email`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        changeEmail: email.value,
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
                            alert(data.result.message);
                            location.href='/mk-board/user/my-page';
                        } else {
                            alert(data.result.message);
                        }
                    })
                    .catch((err) => {
                        alert('이메일 수정 요청 : fetch 에러 ' + err);
                    });

            }
        })
    } 
    
    
    /**
     * 이메일 전체보내기 테스트
     * */
    const emailAllTest = document.querySelector('.emailAllTest');
    if(emailAllTest) {
        emailAllTest.addEventListener('click', function () {
            const userEmail = document.querySelector('.userEmail');
            const userName = document.querySelector('.userName');
            const departmentName = document.querySelector('.departmentName');

            console.log(userEmail);

            // fetch(`/mk-board/user/update/email`, {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //     },
            //     body: JSON.stringify({
            //         changeEmail: email.value,
            //     }),
            // })
            //     .then((res) => {
            //         if (res.status !== 200) {
            //             throw new Error('Network response was not 200');
            //         }
            //         return res.json();
            //     })
            //     .then((data) => {
            //         if(data.result.status === 'success') {
            //             alert(data.result.message);
            //             location.href='/mk-board/user/my-page';
            //         } else {
            //             alert(data.result.message);
            //         }
            //     })
            //     .catch((err) => {
            //         alert('이메일 수정 요청 : fetch 에러 ' + err);
            //     });
        })
    }

    /**
     * 비밀번호 초기화 버튼 이벤트 처리
     * */
    const resetPasswordBtn = document.querySelector('.resetPasswordBtn');
    if(resetPasswordBtn) {
        resetPasswordBtn.addEventListener('click', function () {
            loadingSpinner.style.display = 'block';
            if(codeCheck === 0) {
                alert('인증번호를 확인해주세요.');
            } else {
                const email = document.getElementById('email');
                fetch(`/mk-board/user/reset/password`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        userEmail: email.value,
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
                            alert(data.result.message);
                            location.href='/mk-board';
                        } else {
                            alert(data.result.message);
                        }
                    })
                    .catch((err) => {
                        alert('비밀번호 초기화 요청 : fetch 에러 ' + err);
                    });
            }
        })
    }

    /**
     * 회원 권한 변경 요청 이벤트 등록
     * */
    const userStatusDropdownItems = document.querySelectorAll('.user-status-dropdown-item');
    if(userStatusDropdownItems) {
        userStatusDropdownItems.forEach(function (item) {
            item.addEventListener('click', function () {
                const selectedValue = this.getAttribute('data-value');
                const userEmail = this.closest('.userInfoDashboard').querySelector('.userEmail').textContent.slice(4);

                fetch(`/mk-board/user/update/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        userEmail: userEmail,
                        userStatus: selectedValue
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
                            alert(data.result.message);
                            location.href='/mk-board/user/manage';
                        } else {
                            alert(data.result.message);
                        }
                    })
                    .catch((err) => {
                        alert('비밀번호 초기화 요청 : fetch 에러 ' + err);
                    });
            });
        });
    }

    /**
     * 회원 삭제 요청 이벤트 등록
     * */
    const deleteButtons = document.querySelectorAll('.userDeleteBtn');
    if(deleteButtons) {
        deleteButtons.forEach(function (item) {
            item.addEventListener('click', function () {
                const userEmail = this.closest('.userInfoDashboard').querySelector('.userEmail').textContent.slice(4);
                console.log(userEmail);
                if(confirm('정말 삭제하시겠습니까?')) {
                    fetch(`/mk-board/user/delete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            userEmail: userEmail,
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
                                alert(data.result.message);
                                location.href='/mk-board/user/manage';
                            } else {
                                alert(data.result.message);
                            }
                        })
                        .catch((err) => {
                            alert('회원 삭제 요청 : fetch 에러 ' + err);
                        });

                }
            });
        });
    }

    /**
     * 댓글 수정 요청시 모달창 이벤트
     * */

    const deletePostBtn = document.querySelector('.deletePostBtn');
    if(deletePostBtn) {
        deletePostBtn.addEventListener('submit', function () {
            confirm('정말 삭제하시겠습니까?');
        })
    }


    /**
     * 댓글 수정 요청시 모달창 이벤트
     * */
    const openCommentEditModal = document.querySelectorAll('.openCommentEditModal');
    if(openCommentEditModal) {
        openCommentEditModal.forEach(function (item) {
            item.addEventListener('click', function () {
                const commentIdx = this.closest('.commentBox').querySelector('.commentIdx').value;
                const userEmail = this.closest('.commentBox').querySelector('.userEmail').innerText.slice(2);
                const userName = this.closest('.commentBox').querySelector('.userName').innerText;
                const content = this.closest('.commentBox').querySelector('.content').innerText;

                console.log(commentIdx, userEmail, userName, content);

                const editCommentModal = $('#editCommentModal');
                editCommentModal.find('#commentIdx').val(commentIdx);
                editCommentModal.find('#userName').val(userName);
                editCommentModal.find('#userEmail').val(userEmail);
                editCommentModal.find('#content').text(content);
                editCommentModal.modal('show');
            })
        })
    }



    /**
     * 댓글 수정 완료 요청 이벤트 등록
     * */
    const editCommentModalSubmit = document.querySelector('.editCommentModalSubmit');
    if(editCommentModalSubmit) {
        editCommentModalSubmit.addEventListener('click', function () {
            const commentIdx = this.closest('.editCommentModalForm').querySelector('.commentIdx').value;
            const content = this.closest('.editCommentModalForm').querySelector('.content').value;
            console.log(commentIdx, content);

            fetch(`/mk-board/comment/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    commentIdx: commentIdx,
                    content, content
                }),
            })
                .then((res) => {
                    if (res.status !== 200) {
                        throw new Error('Network response was not 200');
                    }
                    return res.json();
                })
                .then((data) => {
                    if (data.result.status === 'success') {
                        location.href = '/mk-board/post/read?postIdx=' + data.result.postIdx;
                    } else {
                        alert(data.result.message);
                    }
                })
                .catch((err) => {
                    alert('댓글 수정 요청 : fetch 에러 ' + err);
                });
        });
    }



    /**
     * 댓글 삭제 기능
     * */
    const deleteCommentBtn = document.querySelectorAll('.deleteCommentBtn');
    if(deleteCommentBtn) {
        deleteCommentBtn.forEach(function (item) {
            item.addEventListener('click', function () {
                const commentIdx = this.closest('.commentBox').querySelector('.commentIdx').value;
                console.log(commentIdx);

                if(confirm('정말 삭제하시겠습니까?')) {
                    fetch(`/mk-board/comment/delete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            commentIdx: commentIdx,
                        }),
                    })
                        .then((res) => {
                            if (res.status !== 200) {
                                throw new Error('Network response was not 200');
                            }
                            return res.json();
                        })
                        .then((data) => {
                            if (data.result.status === 'success') {
                                location.href = '/mk-board/post/read?postIdx=' + data.result.postIdx;
                            } else {
                                alert(data.result.message);
                            }
                        })
                        .catch((err) => {
                            alert('댓글 삭제 요청 : fetch 에러 ' + err);
                        });
                }
            })
        });
    }

     /**
     * 비밀번호 실시간 유효 검사
     * */
    const changePassword = document.getElementById("changePassword");
    if(changePassword) {
        changePassword.addEventListener('blur', function () {
            const checkMessage = document.getElementById("password-check-message");
            const numCheck = /[0-9]/u;
            const engCheck = /[a-z]/u;
            const speCheck = /[\\!\\@\\#\\$\\%\\^\\&\\*]/u;

            if(numCheck.test(changePassword.value) && engCheck.test(changePassword.value) && speCheck.test(changePassword.value) && (changePassword.value.length >= 8 && changePassword.value.length <= 20)) {
                checkMessage.innerHTML = "비밀번호가 유효합니다."
                checkMessage.style.color = "green"
            } else {
                checkMessage.innerHTML = "영문, 숫자, 특수문자(!,@,#,$,%,^,&,*) 최소 8자리 ~ 최대 20자리";
                checkMessage.style.color = "orange";
            }

        })
    }
    /**
     * 2차 비밀번호 동일 여부 검사
     * */
    const changePasswordCheck = document.getElementById("changePasswordCheck");
    if(changePasswordCheck) {
        changePasswordCheck.addEventListener('blur', function () {
            const password = document.getElementById("changePassword").value;
            const matchMessage = document.getElementById("password-match-message");
            const changePasswordBtn = document.getElementById("changePasswordBtn");

            if(changePasswordCheck.value === password) {
                changePasswordBtn.style.display='block';
                matchMessage.innerHTML = "비밀번호가 일치합니다.";
                matchMessage.style.color = "green";
            } else {
                changePasswordBtn.style.display='none';
                matchMessage.innerHTML = "비밀번호가 일치하지 않습니다.";
                matchMessage.style.color = "orange";
            }
        })
    }


    /**
     * 페이지네이션 페이지 이동
     * */
    const pageLinks = document.querySelectorAll('.page-link');
    if(pageLinks) {
        pageLinks.forEach(function (pageLink) {
            pageLink.addEventListener('click', function () {
                var currentUrl = window.location.href;
                var urlSearchParams = new URLSearchParams(window.location.search);

                // page 파라미터 세팅
                urlSearchParams.set('page', pageLink.getAttribute('data-page'));
                location.href = currentUrl.split('?')[0] + '?' + urlSearchParams.toString();
            });
        });
    }


    /**
     * 게시글 권한 변경 요청 이벤트 등록 (외부에서)
     * */
    const postDropdownItems = document.querySelectorAll('.post-status-dropdown-item');
    if(postDropdownItems) {
        postDropdownItems.forEach(function (item) {
            item.addEventListener('click', function () {
                const selectedValue = this.getAttribute('data-value');
                const postIdx = this.closest('.postInfoDashboard').querySelector('.postIdx').textContent;

                //조회한 적이 있는지 확인하고 있다면, 외부에서 권한 변경 가능!
                const cookieName = `post_views${postIdx}=1`;
                const hasCookie = document.cookie.includes(cookieName);

                if(hasCookie) {
                    if(confirm('정말 변경하시겠습니까?')) {
                        fetch(`/mk-board/post/update/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                postIdx: postIdx,
                                postStatus: selectedValue
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
                                    location.reload();
                                } else {
                                    alert(data.result.message);
                                }
                            })
                            .catch((err) => {
                                alert('게시글 권한 변경 요청 : fetch 에러 ' + err);
                            });
                    }
                } else {
                    alert('게시글을 조회한 뒤 권한 변경을 해주세요.')
                    window.location.href = `/mk-board/post/read?postIdx=${postIdx}`;
                }

            });
        });
    }

    /**
     * 게시글 반려 시 사유 작성 모달창 이벤트
     * */
    const openRejectMessageModal = document.querySelectorAll('.openRejectMessageModal');
    if(openRejectMessageModal) {
        openRejectMessageModal.forEach(function (item) {
            item.addEventListener('click', function () {
                const postIdx = this.closest('.postStatusBox').querySelector('.postIdx').value;
                console.log(postIdx);
                const rejectMessageModal = $('#rejectMessageModal');
                rejectMessageModal.find('#modalPostIdx').val(postIdx);
                rejectMessageModal.modal('show');
            })
        })
    }


    /**
     * 게시글 권한 변경 요청 이벤트 등록 (내부에서)
     * */
    const postStatusOptions = document.getElementsByName('postStatusOptions');
    if(postStatusOptions) {
        postStatusOptions.forEach(function (postStatusOption) {
            postStatusOption.addEventListener('click', function () {
                // 클릭된 라디오 버튼의 값을 가져와서 사용할 수 있습니다.
                const selectedValue = this.value;
                const postIdx = document.getElementById('postIdx').value;

                if(selectedValue === '반려') {

                }

                fetch(`/mk-board/post/update/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        postIdx: postIdx,
                        postStatus: selectedValue
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
                            alert(data.result.message);
                            location.reload();
                        } else {
                            alert(data.result.message);
                        }
                    })
                    .catch((err) => {
                        alert('게시글 권한 변경 요청 : fetch 에러 ' + err);
                    });
            });
        });
    }


    /**
     * 게시글 반려 시 사유 작성 모달창 이벤트(내부에서)
     * */
    const openRejectMessageModal1 = document.getElementById('openRejectMessageModal1');
    if(openRejectMessageModal1) {
        openRejectMessageModal1.addEventListener('click', function () {
            const postIdx = document.getElementById('postIdx').value;
            console.log(postIdx);
            const rejectMessageModal = $('#rejectMessageModal');
            rejectMessageModal.find('#modalPostIdx').val(postIdx);
            rejectMessageModal.modal('show');
        })
    }


    /**
     * 파일 다운로드 기능
     * */

    const handleDownload = (fileIdx, fileOriginName) => {
        fetch(`/mk-board/file/download`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                fileIdx: fileIdx,
            }),
        })
            .then((res) => {
                if (res.status !== 200) {
                    throw new Error('Network response was not 200');
                }
                return res.json();
            })
            .then((data) => {
                if (data.result.status === 'success') {
                    const fileName = data.result.fileName;
                    const targetFilePath = `http://localhost/mk-board/assets/uploads/${fileName}`;

                    // 파일 다운로드를 위한 링크 생성
                    const downloadLink = document.createElement('a');
                    downloadLink.href = targetFilePath;
                    downloadLink.download = fileOriginName;

                    // 링크를 DOM에 추가하고 클릭하여 다운로드 시작
                    document.body.appendChild(downloadLink);
                    downloadLink.click();

                    // 다운로드 후 링크를 DOM에서 제거
                    document.body.removeChild(downloadLink);
                } else {
                    alert(data.result.message);
                }
            })
            .catch((err) => {
                alert('댓글 삭제 요청 : fetch 에러 ' + err);
            });
    };

    const downloadBtn = document.querySelectorAll('.downloadBtn');
    if (downloadBtn) {
        downloadBtn.forEach(function (item) {
            item.addEventListener('click', function () {
                const fileIdx = this.closest('.fileList').querySelector('.fileIdx').innerHTML;
                const fileOriginName = this.closest('.fileList').querySelector('.downloadATag').innerHTML;
                handleDownload(fileIdx, fileOriginName);
            });
        });
    }

    const downloadATag = document.querySelectorAll('.downloadATag');
    if (downloadATag) {
        downloadATag.forEach(function (item) {
            item.addEventListener('click', function () {
                const fileIdx = this.closest('.fileList').querySelector('.fileIdx').innerHTML;
                const fileOriginName = this.closest('.fileList').querySelector('.downloadATag').innerHTML;
                handleDownload(fileIdx, fileOriginName);
            });
        });
    }
});