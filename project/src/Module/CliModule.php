<?php

namespace Queue\Module;

use Perfumer\Framework\Controller\Module;

class CliModule extends Module
{
    public $name = 'queue';

    public $router = 'router.console';

    public $request = 'queue.request';
}