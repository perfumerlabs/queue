<?php

namespace Queue\Queue;

use Perfumer\Framework\Controller\PlainController;

abstract class Worker extends PlainController
{
    abstract protected function getName();

    abstract protected function getQueue();

    public function action()
    {
        /** @var QueueInterface $queue */
        $queue = $this->s($this->getQueue());

        while (true) {
            $task = null;

            if ($task = $queue->get()) {
                $queue->complete($task);

                try {
                    $this->getProxy()->execute('queue', 'queue', 'task', [
                        $task->getUrl(),
                        $task->getMethod(),
                        $task->getHeaders(),
                        $task->getJson(),
                        $task->getQueryString(),
                        $task->getBody(),
                    ]);
                } catch (\Throwable $e) {
                    break;
                }

                if ($task->getSleep() > 0) {
                    usleep($task->getSleep());
                }
            } else {
                sleep(5);
            }

            if (memory_get_usage(true) > 128 * 1024 * 1024) {
                break;
            }
        }
    }
}