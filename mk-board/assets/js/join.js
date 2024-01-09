let idCheck = 0;
const sendIt = () => {  // 회원가입 형식 체크하고 전송하는 함수
    const userName = document.joinForm.userName;
    const userEmail = document.joinForm.userEmail;
    const userPw = document.joinForm.userPw;
    const userPwCheck = document.joinForm.userPwCheck;
    const userDepart = document.joinForm.userDepart;
    const userPhone = document.joinForm.userPhone;
    const expNameText = /[가-힣-A-Za-z]+$/;
    const expHpText = /^\d{3}-\d{3,4}-\d{4}$/;
    const numCheck = /[0-9]/u;
    const engCheck = /[a-z]/u;
    const speCheck = /[\\!\\@\\#\\$\\%\\^\\&\\*]/u;

    // 유저 이름
    if(userName.value == ''){
        alert("이름을 입력하세요.");
        userName.focus();
        return false;
    }
    if(!expNameText.test(userName.value)){
        alert("이름의 형식이 올바르지 않습니다. 한글 또는 영어만 가능합니다.");
        userName.focus();
        return false;
    }

    // 유저 이메일 중복체크
    // if(idCheck == 0){
    //     alert("이메일 중복확인을 해주세요.")
    //     userEmail.focus();
    //     return false;
    // }

    // 유저 패스워드
    if(userPw.value == ''){
        alert("패스워드를 입력해주세요.");
        userPw.focus();
        return false;
    }
    if(userPwCheck.value == '') {
        alert('2차 패스워드를 입력해주세요.');
        userPwCheck.focus();
        return false;
    }
    if(numCheck.test(password) && engCheck.test(password) && speCheck.test(password) && (password.length >= 8 && password.length <= 20)) {
        alert("영문, 숫자, 특수문자(!,@,#,$,%,^,&,*) 최소 8자리 ~ 최대 20자리");
        userPw.focus();
        return false;
    }
    if(userPw.value.length < 8 || userPw.value.length > 20){
        alert("최소 8자리 ~ 최대 20자리를 입력해주세요.");
        userPw.focus();
        return false;
    }
    if(userPw.value != userPwCheck.value) {
        alert('패스워드가 일치하지 않습니다.');
        userPwCheck.focus();
        return false;
    }

    // 유저 휴대폰
    if(!expHpText.test(userPhone.value)) {
        alert('올바르지 않은 휴대폰 형식입니다.');
        userPhone.focus();
        return false;
    }

    return true;
}

const checkEmail = () => { // 사용자 이메일 중복 체크
    const userEmail = document.joinForm.userEmail;
    const expEmailText = /^[A-Za-z0-9\.\-]+@[A-Za-z0-9\.\-]+\.[A-Za-z0-9\.\-]+$/;
    const result = document.querySelector('#result');

    if (userEmail.value == '') {
        alert("이메일을 입력해주세요.");
        userEmail.focus();
        return false;
    }
    if (!expEmailText.test(userEmail.value)) {
        alert('이메일 형식이 올바르지 않습니다.');
        userEmail.focus();
        return false;
    }

    fetch(`/checkUserEmail?userEmail=${userEmail.value}`)
        .then((res) => {
            if(res.status != 200) {
                throw new Error('Network response was not 200');
            }
            return res.json();
        })
        .then((data) => {
            const txt = data.result;
            console.log(txt);
            if (txt === "O") {
                idCheck = 1;
                result.style.display = "block";
                result.style.color = "green";
                result.innerHTML = "&nbsp;&nbsp;&nbsp;사용 가능한 이메일입니다.";
            } else {
                result.style.display = "block";
                result.style.color = "red";
                result.innerHTML = "&nbsp;&nbsp;&nbsp;중복된 이메일입니다.";
                userEmail.focus();
                userEmail.addEventListener("keydown", function () {
                    result.style.display = "none";
                });
            }
        })
        .catch((err) => {
            console.error('There was a problem with the fetch operation : ', err);
        })
};

const checkPassword = () => {
    // 비밀번호 실시간 유효 검사
    const password = document.getElementById("user_pw").value;
    const checkMessage = document.getElementById("password-check-message");
    const numCheck = /[0-9]/u;
    const engCheck = /[a-z]/u;
    const speCheck = /[\\!\\@\\#\\$\\%\\^\\&\\*]/u;

    if(numCheck.test(password) && engCheck.test(password) && speCheck.test(password) && (password.length >= 8 && password.length <= 20)) {
        checkMessage.innerHTML = "비밀번호가 유효합니다!"
        checkMessage.style.color = "green"
    } else {
        checkMessage.innerHTML = "영문, 숫자, 특수문자(!,@,#,$,%,^,&,*) 최소 8자리 ~ 최대 20자리";
        checkMessage.style.color = "orange";
    }
}

const checkPasswordMatch = () => {
    // 2차 비밀번호 동일 여부 검사
    const password = document.getElementById("user_pw").value;
    const passwordCheck = document.getElementById("userpw_ch").value;
    const matchMessage = document.getElementById("password-match-message");

    if(passwordCheck === password) {
        matchMessage.innerHTML = "비밀번호가 일치합니다.";
        matchMessage.style.color = "green";
    } else {
        matchMessage.innerHTML = "비밀번호가 일치하지 않습니다.";
        matchMessage.style.color = "orange";
    }
}