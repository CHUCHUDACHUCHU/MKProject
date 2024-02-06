# 베이스 이미지 설정
FROM ubuntu/apache2:2.4-22.04_beta

# Composer 설치
RUN apt-get update && \
    apt-get install -y composer nano php-mysql libapache2-mod-php

# Apache2 설정 파일 수정
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 파일의 가장 아래에 ServerName localhost 추가
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Rewrite 모듈 활성화
RUN a2enmod rewrite

# 작업 디렉토리 설정
WORKDIR /var/www/html/mk-board

# Composer install 및 PHP Migration 파일 실행
CMD composer install --no-interaction --quiet && php DB/Migration.php && apache2-foreground
