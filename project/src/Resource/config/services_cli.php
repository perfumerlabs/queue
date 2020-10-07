<?php

return [
    'queue.request' => [
        'class' => 'Perfumer\\Framework\\Proxy\\Request',
        'arguments' => ['$0', '$1', '$2', '$3', [
            'prefix' => 'Queue\\Command',
            'suffix' => 'Command'
        ]]
    ]
];
