$(document).ready(function () {
    // 페이지 이동
    $(".page-link").click(function () {
        let currentUrl = window.location.href;
        let urlSearchParams = new URLSearchParams(window.location.search);

        // page 파라미터 세팅
        urlSearchParams.set('page', $(this).attr('data-page'));
        location.href = currentUrl.split('?')[0] + '?' + urlSearchParams.toString();
    })
});