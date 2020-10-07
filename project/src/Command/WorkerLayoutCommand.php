<?php

namespace Queue\Command;

use Perfumer\Framework\Controller\PlainController;
use Perfumer\Framework\Router\ConsoleRouterControllerHelpers;
use Queue\Queue\QueueInterface;
use Queue\Queue\Task;

abstract class WorkerLayoutCommand extends PlainController
{
    use ConsoleRouterControllerHelpers;

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

                if ($task->getType() === Task::TYPE_FRACTION) {
                    if ($task->getGap() < 10 || $task->getMin() > $task->getMax()) {
                        continue;
                    }

                    if ($task->getMax() - $task->getMin() > $task->getGap()) {
                        echo sprintf("FRACTION: Dividing task: min - %s, max - %s, gap - %s", $task->getMin(), $task->getMax(), $task->getGap()) . PHP_EOL;

                        $offset = intval(($task->getMax() - $task->getMin()) / 4);

                        for ($i = 0; $i < 4; $i++) {
                            $new_task = clone $task;
                            $new_task->setMin($task->getMin() + $offset * $i);

                            if ($i === 3) {
                                $new_task->setMax($task->getMax());
                            } else {
                                $new_task->setMax($task->getMin() + $offset * ($i + 1) - 1);
                            }

                            echo sprintf("FRACTION: New task: min - %s, max - %s, gap - %s", $new_task->getMin(), $new_task->getMax(), $new_task->getGap()) . PHP_EOL;

                            $queue->save($new_task);
                        }

                        continue;
                    }
                }

                try {
                    if ($task->getType() === Task::TYPE_FRACTION) {
                        $json = $task->getJson();
                        $query_string = $task->getQueryString();

                        if (!$query_string) {
                            $json['_min'] = $task->getMin();
                            $json['_max'] = $task->getMax();
                            $json['_gap'] = $task->getGap();
                        } else {
                            $query_string['_min'] = $task->getMin();
                            $query_string['_max'] = $task->getMax();
                            $query_string['_gap'] = $task->getGap();
                        }
                    }

                    $this->execute('queue', 'task', [
                        $task->getUrl(),
                        $task->getMethod(),
                        $task->getHeaders(),
                        $task->getJson(),
                        $task->getQueryString(),
                        $task->getBody(),
                    ]);
                } catch (\Throwable $e) {
                    throw $e;
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