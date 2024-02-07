# 베이스 이미지 설정
FROM ubuntu/apache2:2.4-22.04_beta

# Composer 설치
RUN apt-get update && \
    apt-get install -y composer nano php-mysql libapache2-mod-php

# Xdebug 설치
RUN apt-get install -y php-xdebug

# Xdebug 설정 추가
RUN echo "xdebug.mode=debug" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini
RUN echo "xdebug.client_host=host.docker.internal" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini

## 추후 크롬에 xdebug 설치 후, 전원 키고, debug모드 선택하고, IDE key 고르기
## 추후 phpStorm에서 Run/Debug Configuration에서 서버 정보 입력하기.

# Apache2 설정 파일 수정
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 파일의 가장 아래에 ServerName localhost 추가
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Rewrite 모듈 활성화
RUN a2enmod rewrite

# 작업 디렉토리 설정
WORKDIR /var/www/html/mk-board

# Composer install 및 PHP Migration 파일 실행
CMD ["bash", "-c", "composer install --no-interaction --quiet && apache2-foreground"]

#추후 mysql컨테이너가 docker라는 사용자, MKBoardDB 생성하고 나서
#완전히 준비가 되고 나서 php DB/Migration.php 명령어가 실행 될 수 있도록 수정하면 더 좋을 듯!
#지금은 도커 컴포즈 다 돌아가고 좀 있다가 직접 apache2컨테이너 접속해서 직접 php 명령어 실행해야 해,,