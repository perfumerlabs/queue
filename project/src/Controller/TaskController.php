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
            'timeout',
        ]);

        $data['type'] = Task::TYPE_REGULAR;

        $task = $this->createTaskFromArray($data);

        $adapter_name = $this->registerDefinitions($data['worker']);

        /** @var TarantoolAdapter $queue */
        $queue = $this->s($adapter_name);
        $queue->save($task);
    }
}
