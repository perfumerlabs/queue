FROM ubuntu:xenial

LABEL authors="Ilyas Makashev mehmatovec@gmail.com"

RUN set -x \
    && apt-get update && apt-get install -y --no-install-recommends ca-certificates wget locales && rm -rf /var/lib/apt/lists/* \
    && useradd -s /bin/bash -m queue \
    && echo "deb http://nginx.org/packages/ubuntu/ xenial nginx" > /etc/apt/sources.list.d/nginx.list \
    && echo "deb-src http://nginx.org/packages/ubuntu/ xenial nginx" >> /etc/apt/sources.list.d/nginx.list \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu xenial main" > /etc/apt/sources.list.d/php.list \
    && echo "deb-src http://ppa.launchpad.net/ondrej/php/ubuntu xenial main" >> /etc/apt/sources.list.d/php.list \
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
        gnupg2 \
        lsb-release \
        apt-transport-https \
    && curl http://download.tarantool.org/tarantool/2.2/gpgkey | sudo apt-key add - \
    && release=`lsb_release -c -s` \
    && sudo rm -f /etc/apt/sources.list.d/*tarantool*.list \
    && echo "deb http://download.tarantool.org/tarantool/2.2/ubuntu/ ${release} main" | sudo tee /etc/apt/sources.list.d/tarantool_2_2.list \
    && echo "deb-src http://download.tarantool.org/tarantool/2.2/ubuntu/ ${release} main" | sudo tee -a /etc/apt/sources.list.d/tarantool_2_2.list \
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

VOLUME /var/lib/tarantool

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]