FROM ubuntu:bionic

LABEL authors="Ilyas Makashev <mehmatovec@gmail.com>"

ENV TZ 'UTC'

RUN set -x \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        wget \
        locales \
        gnupg2 \
    && rm -rf /var/lib/apt/lists/* \
    && useradd -s /bin/bash -m queue \
    && echo "deb http://nginx.org/packages/ubuntu/ bionic nginx" > /etc/apt/sources.list.d/nginx.list \
    && echo "deb-src http://nginx.org/packages/ubuntu/ bionic nginx" >> /etc/apt/sources.list.d/nginx.list \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu bionic main" > /etc/apt/sources.list.d/php.list \
    && echo "deb-src http://ppa.launchpad.net/ondrej/php/ubuntu bionic main" >> /etc/apt/sources.list.d/php.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys ABF5BD827BD9BF62 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C \
    && apt update \
    && apt install -y \
        nginx \
        php7.4 \
        php7.4-cli \
        php7.4-common \
        php7.4-curl \
        php7.4-fpm \
        php7.4-json \
        php7.4-opcache \
        supervisor \
        iputils-ping \
        vim \
        curl \
        git \
        zip \
        sudo \
        lsb-release \
        apt-transport-https \
    && curl -L https://tarantool.io/gqJDdbI/release/2.6/installer.sh | bash \
    && apt update \
    && apt install -y \
        tarantool \
        tarantool-queue

COPY project /opt/queue
COPY nginx /usr/share/container_config/nginx
COPY supervisor /usr/share/container_config/supervisor
COPY queue.lua /etc/tarantool/instances.enabled/queue.lua
COPY init.sh /usr/local/bin/init.sh
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN set -x\
    && chown -R queue:queue /opt/queue \
    && cd /opt/queue \
    && sudo -u queue php composer.phar install --no-dev --prefer-dist \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/init.sh

ENV QUEUE_WORKERS "{\"default\":1}"
ENV QUEUE_DEFAULT_TIMEOUT 30
ENV DEBUG false
ENV PHP_PM_MAX_CHILDREN 60
ENV PHP_PM_MAX_REQUESTS 500

VOLUME /var/lib/tarantool

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]