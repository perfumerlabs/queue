<?php

namespace Queue\Bundle;

use Perfumer\Component\Container\AbstractBundle;

class CliBundle extends AbstractBundle
{
    public function getName()
    {
        return 'queue';
    }

    public function getDefinitionFiles()
    {
        return [
            __DIR__ . '/../Resource/config/services_cli.php'
        ];
    }

    public function getAliases()
    {
        return [
            'router' => 'router.console',
            'request' => 'queue.request'
        ];
    }
}
