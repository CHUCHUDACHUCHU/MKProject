<!DOCTYPE html>
<html lang="en">
<?php
include __DIR__ . '/../part/head.php';
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
                        <a href="" data-bs-toggle="modal" data-bs-target="#resetPasswordModal"><span>Forgot your Password?</span></a>
                    </div>
                    <div class="modal fade" id="resetPasswordModal" aria-hidden="true" aria-labelledby="resetPasswordModalToggleLabel" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="resetPasswordModalToggleLabel">비밀번호 초기화</h1>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="mb-5">
                                            <li><b>이메일 인증을 하신 후 초기화 버튼을 누르세요.</b></li>
                                            <li><b>해당 이메일로 초기화 된 비밀번호가 제공됩니다.</b></li>
                                        </div>
                                        <div class="mb-3 d-flex">
                                            <input type="email" class="form-control" id="email" >
                                            <input class="btn btn-primary verificationCodeSendBtn" type="button" value="인증번호 발송">
                                        </div>
                                        <div class="container">
                                            <div class="row">
                                                <input type="text" class="form-control col" id="codeInputBox">
                                                <input class="btn btn-primary codeInputBoxBtn col-2" id="codeInputBoxBtn" type="button" value="확인">
                                                <span class="col" id="codeSessionLiveTime"></span>
                                            </div>
                                        </div>
                                        <span id = "codeCheckMessage" style="display: block; font-size: 10px"></span>
                                    </form>
                                    <!-- 로딩 스피너 추가 -->
                                    <div id="loading-spinner" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                        <img src="/mk-board/assets/img/spinner.gif" alt="로딩 중..." /> <!-- 스피너 이미지 등을 넣어주세요 -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button class="btn btn-primary resetPasswordBtn" id="resetPasswordBtn">초기화</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </br>
                    <span style="font-size: small; font-weight: bolder">임시 계정[아이디 : admin, 비밀번호 : c]로 로그인하실 수 있습니다.</span><br>
                    <span style="font-size: small; font-weight: bolder; color: #ff9800">현재 가능한 기능</span>
                    <ul>
                        <li>로그인, 로그아웃 기능</li>
                        <li>전체게시글 확인, 검색, 페이지네이션</li>
                        <li>게시글 작성, 수정, 삭제</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>