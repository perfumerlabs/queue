<?php

namespace Queue\Bundle;

use Perfumer\Component\Container\AbstractBundle;

class SharedBundle extends AbstractBundle
{
    public function getName()
    {
        return 'shared';
    }

    public function getDefinitionFiles()
    {
        return [
            __DIR__ . '/../Resource/config/services_shared.php'
        ];
    }

    public function getResourceFiles()
    {
        return [
            __DIR__ . '/../Resource/config/resources_shared.php'
        ];
    }
}
