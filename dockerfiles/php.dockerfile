#FROM openswoole/swoole:latest-alpine
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    #&& chmod +x /usr/bin/composer  \
    && composer self-update --clean-backups 2.1.6

RUN apk update #&& apk add --no-cache libstdc++  \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev openssl-dev pcre-dev pcre2-dev zlib-dev \
    && docker-php-ext-install sockets && docker-php-source extract && mkdir /usr/src/php/ext/openswoole \
    && url -sfL https://github.com/openswoole/swoole-src/archive/v4.11.1.tar.gz -o swoole.tar.gz  \
    && tar xfz swoole.tar.gz --strip-components=1 -C /usr/src/php/ext/openswoole \
    && docker-php-ext-configure openswoole --enable-http2 --enable-openssl --enable-sockets --enable-swoole-curl --enable-swoole-json  \
    && docker-php-ext-install pcntl -j$(nproc) --ini-name zzz-docker-php-ext-openswoole.ini openswoole \
    && rm -f swoole.tar.gz $HOME/.composer/*-old.phar  \
    && docker-php-source delete && apk del .build-deps

RUN apk add --no-cache ffmpeg supervisor

COPY api .

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions imagick pdo_mysql

COPY php/supervisord.conf /etc/supervisord.conf

RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

#RUN echo "file_uploads = On;\n" \
#    "memory_limit = 1G;\n" \
#    "upload_max_filesize = 1G;\n" \
#    "post_max_size = 1G;\n" \
#    "max_execution_time = 600;\n" \
#    > /usr/local/etc/php/conf.d/uploads.ini

USER laravel

#CMD ["/usr/bin/supervisord", "-n"]
