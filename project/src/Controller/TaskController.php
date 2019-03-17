<?php

namespace Queue\Controller;

use Queue\Queue\Adapter\TarantoolAdapter;
use Queue\Queue\Task;

class TaskController extends LayoutController
{
    public function post()
    {
        $data = $this->f([
            'worker',
            'delay',
            'datetime',
            'url',
            'method',
            'headers',
            'json',
            'query_string',
            'body',
            'sleep',
        ]);

        $task = new Task();

        if ($data['url']) {
            $task->setUrl((string) $data['url']);
        }

        if ($data['method']) {
            $task->setMethod((string) $data['method']);
        }

        if ($data['delay']) {
            $task->setDelay((int) $data['delay']);
        }

        if ($data['datetime']) {
            $task->setDatetime((string) $data['datetime']);
        }

        if ($data['headers']) {
            $task->setHeaders((array) $data['headers']);
        }

        if ($data['json']) {
            $task->setJson((array) $data['json']);
        }

        if ($data['query_string']) {
            $task->setQueryString((array) $data['query_string']);
        }

        if ($data['body']) {
            $task->setBody((string) $data['body']);
        }

        if ($data['sleep']) {
            $task->setSleep((int) $data['sleep']);
        }

        $queue_name = 'tarantool.queue.' . $data['worker'];

        $adapter_name = 'queue.tarantool.' . $data['worker'];

        $this->getContainer()->addDefinitions([
            $queue_name => $queue_definition = [
                'shared' => true,
                'class' => 'Tarantool\\Queue\\Queue',
                'arguments' => ['#tarantool', $data['worker']],
            ],
            $adapter_name => [
                'shared' => true,
                'class' => 'Queue\\Queue\\Adapter\\TarantoolAdapter',
                'arguments' => ['#' . $queue_name]
            ]
        ]);

        /** @var TarantoolAdapter $queue */
        $queue = $this->s($adapter_name);
        $queue->save($task);
    }
}
