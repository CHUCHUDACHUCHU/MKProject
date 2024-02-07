# MK게시판입니다.
## 작업: 추교윤
`목적: 매일경제 PHP 실습용 한 달 프로젝트`<br/>
`기간: 2024-01-08 ~ 2024-02-07`<br/>
`요구사항 기능 기본`
* 권한(관리자/일반)이 있는 파일업로드/다운로드 가능한 게시판
* 관리자/반려 게시글 외, 본인이 업로드한 게시글만 조회 가능.
* 관리자 회원관리, 게시글관리
* 관리자 댓글기능(일반은 댓글 불가능)

`기술 스택`
* Apache2
* PHP 8.1
* MySQL
* PDO
* Composer(phpmailer, league/flysystem)
* Routing (참고 [순서 : index.php -> Route -> View/Controller -> Model -> PDO])
* Docker, docker-compose
* Xdebug


📒 [프로젝트 계획서](https://shining-pantydraco-45f.notion.site/e1dcd2c70fb84f06a14f5cd18ee3284d?v=3e6390bac8b0491aac23b309fb515b3c)

📒 [프로젝트 상세 개발항목](https://shining-pantydraco-45f.notion.site/f1345b87741842ac93c0724afe910d03?v=22a92e6fbe6640f09d913ac8ced96a79)

📒 [프로젝트 이슈](https://shining-pantydraco-45f.notion.site/a3b77c1569784f96b769f3fb877955a9?pvs=25)

<hr/>

### 프로젝트 실행 방법
1. ##### `git clone` 프로젝트 다운로드
2. ##### `cd mk-board` 루트 폴더 이동
3. ##### `config.ini`, `dockerConf.ini` 2개 파일 생성 (config 목록 하단 참고)
4. ##### `docker-compose up --build -d` 도커 컴포즈 실행
5. ##### `docker exec -it mk-board-apache2-1 //bin/bash` 로 apache2 컨테이너 접속
6. ##### `php DB/Migration.php` 컨테이너 내부에서 php 파일 실행
7. ##### 웹 접속 : http://localhost:8060/mk-board

<br/>
<br/>

#### config.ini(위치 /mk-board/config.ini)

DB_USER=""

DB_HOSTNAME="mysql"

DB_PASSWORD=""

DB_NAME=""

PASSWORD_SALT = "" //비밀번호 hash salt

FILE_UPLOAD_PATH = "/var/www/html/mk-board/assets/uploads" //변경해도 상관 X

PASSWORD_INIT = "" //초기 비밀번호

SMTP_EMAIL = "" // 이메일 보내는 기능 사용하기 위해서는 구글 email

SMTP_PASSWORD = "" //smtp 비밀번호 => 구글에 구글 smtp 계정 검색 

SMTP_PORT = 587

SSL_KEY_PATH = "/etc/ssl/private/my-ssl-cert.key"

SSL_CERT_PATH = "/etc/ssl/certs/my-ssl-cert.pem"

SCHEDULER_LOG_PATH = "/var/www/html/mk-board/assets/cron/deleteDB.log"

<br/>
<br/>

#### dockerConf.ini
MYSQL_ROOT_PASSWORD=""

MYSQL_DATABASE=""

MYSQL_USER=""

MYSQL_PASSWORD=""