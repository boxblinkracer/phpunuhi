FROM php:cli

COPY ./phpunuhi.phar /usr/local/bin/phpunuhi
COPY ./run.sh /run.sh

RUN chmod +x /usr/local/bin/phpunuhi \
    && echo "memory_limit=-1" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && apt-get update && apt-get install -y libicu-dev \
    && docker-php-ext-install intl \
    && rm -rf /var/lib/apt/lists/*


WORKDIR /app

ENTRYPOINT ["php", "/usr/local/bin/phpunuhi"]