<?php

return [
    'fast_router' => [
        'shared' => true,
        'init' => function(\Perfumer\Component\Container\Container $container) {
            return \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
                $r->addRoute('POST', '/task', 'task.post');
                $r->addRoute('GET', '/test', 'test.get');
                $r->addRoute('POST', '/test', 'test.post');
            });
        }
    ],

    'queue.router' => [
        'shared' => true,
        'class' => 'Perfumer\\Framework\\Router\\Http\\FastRouteRouter',
        'arguments' => ['#gateway.http', '#fast_router', [
            'data_type' => 'json'
        ]]
    ],

    'queue.request' => [
        'class' => 'Perfumer\\Framework\\Proxy\\Request',
        'arguments' => ['$0', '$1', '$2', '$3', [
            'prefix' => 'Queue\\Controller',
            'suffix' => 'Controller'
        ]]
    ]
];
