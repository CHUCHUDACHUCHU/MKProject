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
                    <form action="/mk-board/auth/login" method="post">
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