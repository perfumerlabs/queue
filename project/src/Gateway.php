<?php

namespace Project;

use Perfumer\Framework\Gateway\CompositeGateway;

class Gateway extends CompositeGateway
{
    protected function configure(): void
    {
        $this->addModule('queue', 'QUEUE_HOST', null, 'http');
        $this->addModule('queue', 'queue',      null, 'cli');
    }
}
