FROM php:7.4-fpm as php

LABEL maintainer=<hericoborgo1@gmail.com>

RUN apt-get update -y && apt-get install -y wget
RUN echo 'Install phpunit' && \
	wget --no-check-certificate -O phpunit https://phar.phpunit.de/phpunit-7.phar && \
	chmod +x phpunit && \
	mv phpunit /usr/local/bin/phpunit

RUN rm /etc/apt/preferences.d/no-debian-php

RUN echo 'Install pdo_mysql' && \
	apt-get update -y && apt-get install -y libpq-dev && \
	docker-php-ext-install mysqli && \
	docker-php-ext-install pdo_mysql && \
	docker-php-ext-enable mysqli pdo_mysql
