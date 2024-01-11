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

    // 네비게이션바 클릭 액티브
    var currentPath = window.location.pathname;

    // Update the active class based on the current URL
    if (currentPath === "/mk-board/post/list") {
        document.getElementById("homeLink").classList.add("active");
    } else if (currentPath === "/mk-board/mypage") { // Adjust the path for MyPage
        document.getElementById("mypageLink").classList.add("active");
    }
    
    // 페이지네이션 페이지 이동
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