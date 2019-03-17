<?php

namespace Queue\Bundle;

use Perfumer\Component\Container\AbstractBundle;

class HttpBundle extends AbstractBundle
{
    public function getName()
    {
        return 'queue';
    }

    public function getDefinitionFiles()
    {
        return [
            __DIR__ . '/../Resource/config/services_http.php'
        ];
    }

    public function getAliases()
    {
        return [
            'router' => 'queue.router',
            'request' => 'queue.request',
            'view' => 'view.status'
        ];
    }
}
