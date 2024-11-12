FROM php:cli

COPY ./phpunuhi.phar /usr/local/bin/phpunuhi
COPY ./run.sh /run.sh

RUN chmod +x /usr/local/bin/phpunuhi

WORKDIR /app

ENTRYPOINT ["php", "/usr/local/bin/phpunuhi"]