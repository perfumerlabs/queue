<?php

return [
    'tarantool' => [
        'shared' => true,
        'init' => function(\Perfumer\Component\Container\Container $container) {
            $connection = new \Tarantool\Client\Connection\StreamConnection();
            $packer = new \Tarantool\Client\Packer\PurePacker();

            return new Tarantool\Client\Client($connection, $packer);
        }
    ],
];