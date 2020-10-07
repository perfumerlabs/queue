<?php

namespace Queue\Controller;

use Queue\Queue\Adapter\TarantoolAdapter;
use Queue\Queue\Task;

class FractionController extends LayoutController
{
    public function post()
    {
        $data = $this->f([
            'worker',
            'url',
            'method',
            'headers',
            'json',
            'query_string',
            'body',
            'min',
            'max',
            'gap',
        ]);

        $data['type'] = Task::TYPE_FRACTION;

        $task = $this->createTaskFromArray($data);

        $adapter_name = $this->registerDefinitions($data['worker']);

        /** @var TarantoolAdapter $queue */
        $queue = $this->s($adapter_name);
        $queue->save($task);
    }
}
