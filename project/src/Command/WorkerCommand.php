<?php

namespace Queue\Command;

class WorkerCommand extends WorkerLayoutCommand
{
    protected $adapter;

    protected function getName()
    {
        return $this->o('tube');
    }

    protected function getQueue()
    {
        if (!$this->adapter) {
            $queue_name = 'tarantool.queue.' . $this->getName();

            $adapter_name = 'queue.tarantool.' . $this->getName();

            $this->getContainer()->addDefinitions([
                $queue_name => $queue_definition = [
                    'shared' => true,
                    'class' => 'Tarantool\\Queue\\Queue',
                    'arguments' => ['#tarantool', $this->getName()],
                ],
                $adapter_name => [
                    'shared' => true,
                    'class' => 'Queue\\Queue\\Adapter\\TarantoolAdapter',
                    'arguments' => ['#' . $queue_name]
                ]
            ]);

            $this->adapter = $adapter_name;
        }

        return $this->adapter;
    }
}
