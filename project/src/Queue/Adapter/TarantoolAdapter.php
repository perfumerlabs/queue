<?php

namespace Queue\Queue\Adapter;

use Queue\Queue\QueueInterface;
use Queue\Queue\Task;
use Tarantool\Queue\Queue;

class TarantoolAdapter implements QueueInterface
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function save(Task $task)
    {
        $options = [];

        if ($task->getDelay()) {
            $options['delay'] = (int) $task->getDelay();
        } elseif ($task->getDatetime()) {
            $current = new \DateTime();
            $future = new \DateTime($task->getDatetime());

            if ($future > $current) {
                $options['delay'] = $future->getTimestamp() - $current->getTimestamp();
            }
        }

        if ($task->getJson()) {
            $task->setQueryString([]);
            $task->setBody(null);
        } elseif ($task->getQueryString()) {
            $task->setBody(null);
        }

        $data = [
            'type' => $task->getType(),
            'url' => $task->getUrl(),
            'method' => $task->getMethod(),
            'headers' => $task->getHeaders(),
            'json' => $task->getJson(),
            'query_string' => $task->getQueryString(),
            'body' => $task->getBody(),
            'sleep' => $task->getSleep(),
        ];

        if ($task->getType() === Task::TYPE_FRACTION) {
            $data['min'] = $task->getMin();
            $data['max'] = $task->getMax();
            $data['gap'] = $task->getGap();
        }

        $this->queue->put($data, $options);
    }

    public function complete(Task $task)
    {
        if ($task->getId() === null) {
            return;
        }

        $this->queue->ack($task->getId());
    }

    public function get(): ?Task
    {
        $tarantool_task = $this->queue->take(1);

        $task = null;

        if ($tarantool_task) {
            $data = $tarantool_task->getData();

            $type = $data['type'] ?? Task::TYPE_REGULAR;
            $url = $data['url'] ?? null;
            $method = $data['method'] ?? null;
            $headers = $data['headers'] ?? [];
            $json = $data['json'] ?? [];
            $query_string = $data['query_string'] ?? [];
            $body = $data['body'] ?? null;
            $sleep = $data['sleep'] ?? 0;
            $min = $data['min'] ?? 0;
            $max = $data['max'] ?? 0;
            $gap = $data['gap'] ?? 0;

            $task = new Task();
            $task->setType((string) $type);
            $task->setUrl((string) $url);
            $task->setMethod((string) $method);
            $task->setHeaders((array) $headers);
            $task->setJson((array) $json);
            $task->setQueryString((array) $query_string);
            $task->setBody((string) $body);
            $task->setSleep((int) $sleep);
            $task->setMin((int) $min);
            $task->setMax((int) $max);
            $task->setGap((int) $gap);
            $task->setId($tarantool_task->getId());
        }

        return $task;
    }

    public function delete(Task $task)
    {
        if ($task->getId() === null) {
            return;
        }

        $this->queue->delete($task->getId());
    }
}