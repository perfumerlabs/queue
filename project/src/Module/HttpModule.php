<?php

namespace Queue\Module;

use Perfumer\Framework\Controller\Module;

class HttpModule extends Module
{
    public $name = 'queue';

    public $router = 'queue.router';

    public $request = 'queue.request';

    public $components = [
        'view' => 'view.status',
    ];
}