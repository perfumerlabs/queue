<?php

namespace Queue\Controller;

use Perfumer\Framework\Router\ConsoleRouterControllerHelpers;
use Queue\Queue\Worker;

class WorkerController extends Worker
{
    use ConsoleRouterControllerHelpers;

    protected $adapter;

    protected function getName()
    {
        return $this->o('tube');
    }

    protected function getQueue()
    {
        echo "WorkerController->getQueue";
                if (!$this->adapter) {
            $queue_name = 'tarantool.queue.' . $this->getName();

            $adapter_name = 'queue.tarantool.' . $this->getName();

            $this->getContainer()->addDefinitions([
                /*
                $queue_name => $queue_definition = [
                    'shared' => true,
                    'class' => 'Tarantool\\Queue\\Queue',
                    'arguments' => ['#tarantool', $this->getName()],
                ],
                */
                                $adapter_name => [
'init' => function() {
                        $name = '/Queue/Queue/Pgq/Queue';
                        $src_constr = 'host=' . getenv('PG_HOST') . ' port=' . getenv('PG_PORT') . ' dbname=' . getenv('PG_DATABASE') . ' user=' . getenv('PG_USER') . ' password=' . getenv('PG_PASSWORD');
                        $queue = new \Queue\Queue\Pgq\Queue('cname', 'qname', 2, [$name, "start"], $src_constr);
                return new \Queue\Queue\Adapter\PgqAdapter($queue);
                    }
                    /*
                    'shared' => true,
                                        'class' => 'Queue\\Queue\\Adapter\\TarantoolAdapter',
                    'arguments' => ['#' . $queue_name]
                    */
                ]
                ]);

            $this->adapter = $adapter_name;
        }

        return $this->adapter;
    }
}
