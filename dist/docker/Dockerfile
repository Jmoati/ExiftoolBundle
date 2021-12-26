FROM jmoati/php8-cli

RUN apt-get update && \
    apt-get install -y exiftool

RUN wget https://getcomposer.org/download/latest-stable/composer.phar && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer
