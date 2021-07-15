<?php

namespace Queue\Controller;

use Perfumer\Framework\Controller\ViewController;
use Perfumer\Framework\Router\Http\FastRouteRouterControllerHelpers;
use Perfumer\Framework\View\StatusViewControllerHelpers;
use Queue\Queue\Task;

class LayoutController extends ViewController
{
    use FastRouteRouterControllerHelpers;
    use StatusViewControllerHelpers;

    protected function createTaskFromArray(array $data)
    {
        $url = $data['url'] ?? null;
        $method = $data['method'] ?? null;
        $delay = $data['delay'] ?? null;
        $datetime = $data['datetime'] ?? null;
        $headers = $data['headers'] ?? null;
        $json = $data['json'] ?? null;
        $query_string = $data['query_string'] ?? null;
        $body = $data['body'] ?? null;
        $sleep = $data['sleep'] ?? null;
        $timeout = $data['timeout'] ?? null;
        $min = $data['min'] ?? null;
        $max = $data['max'] ?? null;
        $gap = $data['gap'] ?? null;
        $type = $data['type'] ?? Task::TYPE_REGULAR;

        $task = new Task();

        if ($type) {
            $task->setType((string) $type);
        }

        if ($url) {
            $task->setUrl((string) $url);
        }

        if ($method) {
            $task->setMethod((string) $method);
        }

        if ($delay) {
            $task->setDelay((int) $delay);
        }

        if ($datetime) {
            $task->setDatetime((string) $datetime);
        }

        if ($headers) {
            $task->setHeaders((array) $headers);
        }

        if ($json) {
            $task->setJson((array) $json);
        }

        if ($query_string) {
            $task->setQueryString((array) $query_string);
        }

        if ($body) {
            $task->setBody((string) $body);
        }

        if ($sleep) {
            $task->setSleep((int) $sleep);
        }

        if ($timeout) {
            $task->setTimeout((int) $timeout);
        }

        if ($min) {
            $task->setMin((int) $min);
        }

        if ($max) {
            $task->setMax((int) $max);
        }

        if ($gap) {
            $task->setGap((int) $gap);
        }

        return $task;
    }

    protected function registerDefinitions($worker): string
    {
        $queue_name = 'tarantool.queue.' . $worker;

        $adapter_name = 'queue.tarantool.' . $worker;

        $this->getContainer()->addDefinitions([
            $queue_name => [
                'shared' => true,
                'class' => 'Tarantool\\Queue\\Queue',
                'arguments' => ['#tarantool', $worker],
            ],
            $adapter_name => [
                'shared' => true,
                'class' => 'Queue\\Queue\\Adapter\\TarantoolAdapter',
                'arguments' => ['#' . $queue_name]
            ]
        ]);

        return $adapter_name;
    }
}
