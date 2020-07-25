#!/usr/bin/env bash

sed -i "s/{\"default\": 1}/$QUEUE_WORKERS/g" /opt/queue/workers.php
/usr/bin/php /opt/queue/supervisor.php
/usr/bin/php /opt/queue/queue.php

set -x \
&& rm -rf /etc/nginx \
&& rm -rf /etc/supervisor \
&& mkdir /run/php

set -x \
&& cp -r "/usr/share/container_config/nginx" /etc/nginx \
&& cp -r "/usr/share/container_config/supervisor" /etc/supervisor

sed -i "s/server_name queue;/server_name $QUEUE_HOST;/g" /etc/nginx/sites/queue.conf

sed -i "s/error_log = \/var\/log\/php7.4-fpm.log/error_log = \/dev\/stdout/g" /etc/php/7.4/fpm/php-fpm.conf
sed -i "s/;error_log = syslog/error_log = \/dev\/stdout/g" /etc/php/7.4/fpm/php.ini
sed -i "s/;error_log = syslog/error_log = \/dev\/stdout/g" /etc/php/7.4/cli/php.ini
sed -i "s/log_errors = Off/log_errors = On/g" /etc/php/7.4/cli/php.ini
sed -i "s/log_errors = Off/log_errors = On/g" /etc/php/7.4/fpm/php.ini
sed -i "s/log_errors_max_len = 1024/log_errors_max_len = 0/g" /etc/php/7.4/cli/php.ini
sed -i "s/user = www-data/user = queue/g" /etc/php/7.4/fpm/pool.d/www.conf
sed -i "s/group = www-data/group = queue/g" /etc/php/7.4/fpm/pool.d/www.conf
sed -i "s/pm = dynamic/pm = static/g" /etc/php/7.4/fpm/pool.d/www.conf
sed -i "s/pm.max_children = 5/pm.max_children = 60/g" /etc/php/7.4/fpm/pool.d/www.conf
sed -i "s/;pm.max_requests = 500/pm.max_requests = 500/g" /etc/php/7.4/fpm/pool.d/www.conf
sed -i "s/listen.owner = www-data/listen.owner = queue/g" /etc/php/7.4/fpm/pool.d/www.conf
sed -i "s/listen.group = www-data/listen.group = queue/g" /etc/php/7.4/fpm/pool.d/www.conf
sed -i "s/;catch_workers_output = yes/catch_workers_output = yes/g" /etc/php/7.4/fpm/pool.d/www.conf

sed -i "s/'domain' => 'queue'/'domain' => '$QUEUE_HOST'/g" /opt/queue/src/Resource/config/resources_shared.php

touch /node_status_inited
