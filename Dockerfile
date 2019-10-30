FROM php:7.2-fpm as php

LABEL maintainer=<hericoborgo1@gmail.com>

RUN apt-get update -y && apt-get install -y wget
RUN echo 'Install phpunit' && \
	wget --no-check-certificate -O phpunit https://phar.phpunit.de/phpunit-7.phar && \
	chmod +x phpunit && \
	mv phpunit /usr/local/bin/phpunit

RUN echo 'Install composer' && \
	wget --no-check-certificate -O composer https://getcomposer.org/composer.phar && \
	chmod +x composer && \
	mv composer /usr/local/bin/composer

RUN rm /etc/apt/preferences.d/no-debian-php

RUN echo 'Install pdo_pgsql' && \
	apt-get update -y && apt-get install -y libpq-dev && \
	docker-php-ext-install -j$(nproc) pgsql && \
	docker-php-ext-install -j$(nproc) pdo_pgsql && \
	docker-php-ext-enable pgsql pdo_pgsql

FROM postgres:9.4 as postgres

LABEL maintainer=<hericoborgo1@gmail.com>

RUN chmod -R 777 /var/lib/postgresql
