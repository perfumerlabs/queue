<?php

return [
    'gateway' => [
        'shared' => true,
        'class' => 'Project\\Gateway',
        'arguments' => ['#application', '#gateway.http', '#gateway.console']
    ],

    'tarantool' => [
        'shared' => true,
        'init' => function(\Perfumer\Component\Container\Container $container) {
            $connection = new \Tarantool\Client\Connection\StreamConnection();
            $packer = new \Tarantool\Client\Packer\PurePacker();

            return new Tarantool\Client\Client($connection, $packer);
        }
    ],
];