document.addEventListener('DOMContentLoaded', function() {

    const startInterval = (seconds, callback) => {
        callback();
        return setInterval(callback, seconds * 1000);
    };
    let remainingTime = 1800;
    var currentPage = window.location.pathname;

    // 헤더 네비게이션 표시
    if (currentPage.includes("main.php")) {
        document.getElementById("homeLink").classList.add('active');
    } else if (currentPage.includes("myinfo.php")) {
        document.getElementById("myPageLink").classList.add('active');
    }

    //로그아웃 버튼 이벤트
    //작동 원리
    //이벤트리스너등록 => 클릭 시 fetch(logout_ok.php) => success true라면, alert로그아웃 이후, index.php로 이동
    document.getElementById('logoutButton').addEventListener('click', function (e) {
        e.preventDefault();

        fetch('./logout_ok.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('로그아웃되었습니다.');
                    location.href = '../../index.php';
                } else {
                    alert('로그아웃에 실패하였습니다.');
                }
            })
            .catch(err => {
                console.log('err : ', err);
            })
    });

    //남은 시간 체킹
    //remaingTime 1초마다 1씩 감소, 그리고 시분초형식으로 화면에 출력
    //remaingTime 0초 될 경우, 세션 내 데이터 삭제(unset) => 성공 시, 세션 만료 출력 이후 index.php 보임.
    const interval = startInterval(1, function () {
        // 이 부분에서 요소를 찾을 수 있도록 실행 시점을 보장합니다.
        const sessionTimeElement = document.getElementById('sessionTime');
        if (sessionTimeElement) {
            sessionTimeElement.textContent = secToTime(remainingTime);
            remainingTime--;

            if(remainingTime < 0) {
                clearInterval(interval);
                fetch('./logout_ok.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('세션이 만료되었습니다!');
                            location.href = '../../index.php';
                        } else {
                            alert('로그아웃에 실패했습니다.');
                        }
                    })
                    .catch(err => {
                        console.log('err : ', err);
                    })
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

        return hours + ":" + minutes + ":" + seconds;
    }

});