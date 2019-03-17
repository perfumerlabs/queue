<?php

$workers = require '/opt/queue/workers.php';

$workers = json_decode($workers, true);

foreach ($workers as $name => $nb_processes) {
    $config = "

[program:worker_$name]
command=/usr/bin/php cli queue worker --tube=$name
process_name=%(program_name)s_%(process_num)02d
user=queue
numprocs=$nb_processes
directory=/opt/queue
autostart=true
autorestart=true
priority=20
stdout_events_enabled=true
stderr_events_enabled=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stdout
stderr_logfile_maxbytes=0

    ";

    file_put_contents('/usr/share/container_config/supervisor/supervisord.conf', $config, FILE_APPEND | LOCK_EX);
}
