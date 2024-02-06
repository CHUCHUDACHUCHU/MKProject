# 베이스 이미지 설정
FROM ubuntu/apache2:2.4-22.04_beta

# Composer 설치
RUN apt-get update && \
    apt-get install -y composer nano php-mysql libapache2-mod-php

## apache2.conf 파일 수정 (Directory 설정 변경)
#RUN sed -i '/<Directory \/var\/www>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
#
## 000-default.conf 파일 수정 (ServerName 주석 해제)
#RUN sed -i 's/#ServerName www.example.com/ServerName localhost/g' /etc/apache2/sites-available/000-default.conf

# Rewrite 모듈 활성화
RUN a2enmod rewrite

# Apache 서비스 재시작 및 /var/www/html/mk-board 디렉토리로 이동하여 composer install 실행
CMD service apache2 start && cd /var/www/html/mk-board && composer install --no-interaction --quiet && tail -f /dev/null