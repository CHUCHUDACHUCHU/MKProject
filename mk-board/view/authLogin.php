<!DOCTYPE html>
<html lang="en">
<?php
include "part/head.php";
?>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">MK게시판</h2>
                </div>
                <div class="card-body">
                    <form action="./login" method="post">
                        <div class="form-group">
                            <label>userEmail</label>
                            <input type="text" class="form-control" name="userEmail" placeholder="Enter your email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="userPw" placeholder="Enter your password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="#">Forgot your password?</a>
                    </div>
                    <br></br>
                    <span style="font-size: small; font-weight: bolder">임시 계정[아이디 : a, 비밀번호 : a]로 로그인하실 수 있습니다.</span><br>
                    <span style="font-size: small; font-weight: bolder; color: #ff9800">현재 가능한 기능</span>
                    <ul>
                        <li>로그인, 로그아웃 기능</li>
                        <li>전체게시글 확인, 검색, 페이지네이션</li>
                        <li>게시글 작성, 수정</li>
                    </ul>
                </div>
            </div>


<!--            계정 생성 부분... 나중에 참고 -->
<!--            <br></br>-->
<!--            <br></br>-->
<!--            <div class="card">-->
<!--                <div class="card-header">-->
<!--                    <h2 class="text-center">계정생성</h2>-->
<!--                </div>-->
<!--                <div class="card-body">-->
<!--                    <form action="/mk-board/user/create" method="post">-->
<!--                        <div class="form-group">-->
<!--                            <label>userName</label>-->
<!--                            <input type="text" class="form-control" name="userName" placeholder="Enter your userName">-->
<!--                        </div><div class="form-group">-->
<!--                            <label>userEmail</label>-->
<!--                            <input type="text" class="form-control" name="userEmail" placeholder="Enter your userEmail">-->
<!--                        </div><div class="form-group">-->
<!--                            <label>userPw</label>-->
<!--                            <input type="password" class="form-control" name="userPw" placeholder="Enter your userPw">-->
<!--                        </div>-->
<!--                        <div class="form-group">-->
<!--                            <label>userDepart</label>-->
<!--                            <input type="text" class="form-control" name="userDepart" placeholder="Enter your userDepart">-->
<!--                        </div><div class="form-group">-->
<!--                            <label>userPhone</label>-->
<!--                            <input type="text" class="form-control" name="userPhone" placeholder="Enter your userPhone">-->
<!--                        </div>-->
<!--                        <button type="submit">계정생성</button>-->
<!--                    </form>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</div>
</body>
</html>