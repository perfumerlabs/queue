<?php

$workers = require '/opt/queue/workers.php';

$workers = json_decode($workers, true);

foreach ($workers as $name => $nb_processes) {
    $config = "
queue.create_tube('$name', 'fifottl', { if_not_exists=true })
";

    file_put_contents('/etc/tarantool/instances.enabled/queue.lua', $config, FILE_APPEND | LOCK_EX);
}
