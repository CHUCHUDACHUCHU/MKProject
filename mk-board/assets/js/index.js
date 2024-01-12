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
     * 네이게이션바 세션타임
     * */
    const startInterval = (seconds, callback) => {
        callback();
        return setInterval(callback, seconds * 1000);
    };
    let remainingTime = 1800;

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
                location.href='/mk-board/auth/session'
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
        document.getElementById("homeLink").classList.add("active");
    } else if (currentPath === "/mk-board/mypage") { // Adjust the path for MyPage
        document.getElementById("mypageLink").classList.add("active");
    }


    /**
     * 페이지네이션 페이지 이동
     * */
    var pageLinks = document.querySelectorAll('.page-link');
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