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
            'min',
            'max',
            'gap',
        ]);

        $data['type'] = Task::TYPE_FRACTION;

        $min = (int) $data['min'];
        $max = (int) $data['max'];
        $gap = (int) $data['gap'];

        if (!$min || !$max || !$gap) {
            $this->forward('error', 'badRequest', ["\"Min\", \"max\", \"gap\" parameters must be set"]);
        }

        if ($min > $max) {
            $this->forward('error', 'badRequest', ["\"Min\" must not be greater than \"max\""]);
        }

        if ($gap < 10) {
            $this->forward('error', 'badRequest', ["\"Gap\" must be greater than 10"]);
        }

        $task = $this->createTaskFromArray($data);

        $adapter_name = $this->registerDefinitions($data['worker']);

        /** @var TarantoolAdapter $queue */
        $queue = $this->s($adapter_name);
        $queue->save($task);
    }
}
