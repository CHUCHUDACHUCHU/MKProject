// $(document).ready(function () {
//     // 페이지 이동
//     $(".page-link").click(function () {
//         let currentUrl = window.location.href;
//         let urlSearchParams = new URLSearchParams(window.location.search);
//
//         // page 파라미터 세팅
//         urlSearchParams.set('page', $(this).attr('data-page'));
//         location.href = currentUrl.split('?')[0] + '?' + urlSearchParams.toString();
//     })
// });


document.addEventListener('DOMContentLoaded', function () {

    /**
     * 네이게이션바 로그인세션타임
     * */
    const startInterval = (seconds, callback) => {
        callback();
        return setInterval(callback, seconds * 1000);
    };

    //남은 시간 체킹
    //remaingTime 1초마다 1씩 감소, 그리고 시분초형식으로 화면에 출력
    //remaingTime 0초 될 경우, 세션 내 데이터 삭제(unset) => 성공 시, 세션 만료 출력 이후 index.php 보임.
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
        var seconds = Math.floor(duration % 60),
            minutes = Math.floor((duration / 60) % 60),
            hours = Math.floor((duration / (60 * 60)) % 24);

        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        seconds = (seconds < 10) ? "0" + seconds : seconds;

        return minutes + ":" + seconds;
    }


    /**
     * 네비게이션바 클릭 액티브
     * */
    var currentPath = window.location.pathname;

    // Update the active class based on the current URL
    if (currentPath === "/mk-board/post/list") {
        document.getElementById("homeNav").classList.add("active");
    } else if (currentPath === "/mk-board/user/my-page") { // Adjust the path for MyPage
        document.getElementById("myPageNav").classList.add("active");
    }

    /**
     * 이메일 인증번호 확인 이벤트처리
     * */
    let authCertCheck = 0;
    const result = document.getElementById('authCertCheckMessage');
    const authInputBoxBtn = document.querySelector('.authInputBoxBtn');
    if(authInputBoxBtn) {
        authInputBoxBtn.addEventListener('click', function () {
            const authInputBox = document.getElementById('authInputBox');
            fetch(`/mk-board/user/check-cert`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: authInputBox.value,
                }),
            })
                .then((res) => {
                    if(res.status != 200) {
                        throw new Error('Network response was not 200');
                    }
                    return res.json();
                }).then((data) => {
                const txt = data.result;
                console.log(txt);
                if (txt === "success") {
                    authCertCheck = 1;
                    result.style.display = "block";
                    result.style.color = "green";
                    result.innerHTML = "&nbsp;&nbsp;&nbsp;인증이 완료되었습니다.";
                } else if(txt === 'wrong') {
                    result.style.display = "block";
                    result.style.color = "red";
                    result.innerHTML = "&nbsp;&nbsp;&nbsp;인증번호를 다시 확인해주세요.";
                    authInputBox.focus();
                    authInputBox.addEventListener("keydown", function () {
                        result.style.display = "none";
                    });
                } else {
                    alert('여기야!!!', txt);
                }
            })
                .catch((err) => {
                    console.error('There was a problem with the fetch operation : ', err);
                })
        })
    }


    /**
     * 이메일 인증번호 발송 이벤트처리
     * */
    const emailAuthModalBtn = document.querySelector('.emailAuthModalBtn');
    if(emailAuthModalBtn) {
        emailAuthModalBtn.addEventListener('click', function () {
            const expEmailText = /^[A-Za-z0-9\.\-]+@[A-Za-z0-9\.\-]+\.[A-Za-z0-9\.\-]+$/;
            const changeEmail = document.getElementById('changeEmail');
            const userName = document.getElementById('userName');

            if (changeEmail.value == '') {
                alert("이메일을 입력해주세요.");
                changeEmail.focus();
                return false;
            }
            if (!expEmailText.test(changeEmail.value)) {
                alert('이메일 형식이 올바르지 않습니다.');
                changeEmail.focus();
                return false;
            }

            fetch(`/mk-board/user/send-cert`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    changeEmail: changeEmail.value,
                    userName: userName.value,
                }),
            })
                .then((res) => {
                    if (res.status !== 200) {
                        throw new Error('Network response was not 200');
                    }
                    return res.json();
                })
                .then((data) => {
                    if(data.result === "success") {
                        document.getElementById('authBox').style.display = 'flex';
                        document.getElementById('authInputBox').style.display = 'block';
                        document.getElementById('authInputBoxBtn').style.display = 'block';
                        document.getElementById('authCertTime').style.display = 'block';
                        //이메일인증 세션타임
                        let authCertSessionTime = 180;
                        const intervalAuthCertSessionTime = startInterval(1, function () {
                            const sessionTimeElement = document.getElementById('authCertTime');
                            if (sessionTimeElement) {
                                sessionTimeElement.textContent = secToTime(authCertSessionTime);
                                authCertSessionTime--;

                                if(authCertSessionTime < 0 || authCertCheck == 1) {
                                    clearInterval(intervalAuthCertSessionTime);
                                    fetch(`/mk-board/user/send-cert/sessionout?authCertCheck=${authCertCheck}`)
                                        .then((res) => {
                                            if (res.status !== 200) {
                                                throw new Error('Network response was not 200');
                                            }
                                            return res.json();
                                        })
                                }
                            }
                        })
                    } else if(data.result === "fail") {
                        alert('이메일 전송에 실패하였습니다.');
                    } else if(data.result === "busy") {
                        alert('인증번호가 이미 발송되었습니다. 3분 후 다시 시도해주세요.');
                    } else {
                        alert(data);
                    }
                })
                .catch((err) => {
                    alert(err);
                });
        })
    }

    /**
     * 이메일 수정 버튼 이벤트 처리
     * */
    const emailUpdateBtn = document.querySelector('.emailUpdateBtn');
    if(emailAuthModalBtn) {
        emailUpdateBtn.addEventListener('click', function () {
            if(authCertCheck == 0) {
                alert('인증번호를 확인해주세요.')
            } else {
                const changeEmail = document.getElementById('changeEmail');

                fetch(`/mk-board/user/update/email`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        changeEmail: changeEmail.value,
                    }),
                })
                    .then((res) => {
                        if (res.status !== 200) {
                            throw new Error('Network response was not 200');
                        }
                        return res.json();
                    })
                    .then((data) => {
                        if(data.result === 'success') {
                            alert('이메일 변경에 성공하였습니다.');
                            location.href = '/mk-board/user/my-page';
                        } else if(data.result === 'fail') {
                            alert('이메일 변경에 실패하였습니다.');
                        } else {
                            alert('입력값에 오류가 생겼습니다.')
                        }
                    })
                    .catch((err) => {
                        alert(err);
                    });

            }
        })
    }

    /**
     * 페이지네이션 페이지 이동
     * */
    const pageLinks = document.querySelectorAll('.page-link');
    pageLinks.forEach(function (pageLink) {
        pageLink.addEventListener('click', function () {
            var currentUrl = window.location.href;
            var urlSearchParams = new URLSearchParams(window.location.search);

            // page 파라미터 세팅
            urlSearchParams.set('page', pageLink.getAttribute('data-page'));
            location.href = currentUrl.split('?')[0] + '?' + urlSearchParams.toString();
        });
    });
});