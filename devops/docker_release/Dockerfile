FROM php:8.3.13-cli-alpine3.20

COPY --chmod=0755 ./phpunuhi.phar /usr/local/bin/phpunuhi
COPY --chmod=0755 ./run.sh /run.sh

RUN echo "memory_limit=-1" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && apk add --no-cache \
       icu-dev \
       aspell \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && rm -rf /var/cache/apk/*


WORKDIR /app

ENTRYPOINT ["php", "/usr/local/bin/phpunuhi"]