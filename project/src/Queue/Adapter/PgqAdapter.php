<?php
namespace Queue\Queue\Adapter;

use Queue\Queue\QueueInterface;
use Queue\Queue\Task;
use Queue\Queue\Pgq\Queue;
use pgq\PGQEvent;

class PgqAdapter implements QueueInterface
{

    private $queue;

    private $log;

    private $connectionString;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
        $this->log = new \pgq\SimpleLogger(VERBOSE, "/opt/queue/logs/pgq.log");
        $this->connectionString = 'host=' . getenv('PG_HOST') . ' port=' . getenv('PG_PORT') . ' dbname=' . getenv('PG_DATABASE') . ' user=' . getenv('PG_USER') . ' password=' . getenv('PG_PASSWORD');
    }

    public function save(Task $task)
    {
        $row = [
            "ev_id" => $task->getId(),
            "ev_time" => $task->getDatetime(),
            "ev_txid" => $task->getId(),
            "ev_type" => $task->getMethod(),
            "ev_data" => http_build_query($task->getQueryString()),
            "ev_extra2" => '', // old_data
            "ev_extra1" => 'pgq', // table,
            "ev_retry" => $task->getDelay() ? $task->getDelay() : 18000
        ];
        if (array_key_exists("ev_failed_reason", $row)) {
            $this->failed_reason = $row["ev_failed_reason"];
        }
        if (array_key_exists("ev_failed_time", $row)) {
            $this->failed_time = $row["ev_failed_time"];
        }

        $event = new PgqEvent($this->log, $row);
        $connection = pg_connect($this->connectionString);
        pg_query_params($connection, "select * from pgq.insert_event($1, $2, $3)", [
            $this->queue->getQname(),
            $event->type,
            $event->__toString()
        ]);

        /*
         * $options = [];
         *
         * if ($task->getDelay()) {
         * $options['delay'] = (int) $task->getDelay();
         * } elseif ($task->getDatetime()) {
         * $current = new \DateTime();
         * $future = new \DateTime($task->getDatetime());
         *
         * if ($future > $current) {
         * $options['delay'] = $future->getTimestamp() - $current->getTimestamp();
         * }
         * }
         *
         * if ($task->getJson()) {
         * $task->setQueryString([]);
         * $task->setBody(null);
         * } elseif ($task->getQueryString()) {
         * $task->setBody(null);
         * }
         *
         * $data = [
         * 'url' => $task->getUrl(),
         * 'method' => $task->getMethod(),
         * 'headers' => $task->getHeaders(),
         * 'json' => $task->getJson(),
         * 'query_string' => $task->getQueryString(),
         * 'body' => $task->getBody(),
         * 'sleep' => $task->getSleep(),
         * ];
         *
         * event->
         */
    }

    public function complete(Task $task)
    {
        if ($task->getId() === null) {
            return;
        }
        $batch = $this->queue->next_batch();
        if (! $batch) {
            return;
        } // if no batch
        $events = $this->queue->get_batch_events($batch->getId());
        if (empty($events)) {
            return;
        } // if empty
        foreach ($events as $event) {
            if ($event->getId() == $task->getId()) {
                $this->queue->process_event($event);
                break;
            } // if found
        } // foreach event
    }

    public function get(): ?Task
    {
        $task = null;

        $batch = $this->queue->next_batch();
        if ($batch) {
            $events = $this->queue->get_batch_events($batch->getId());
            if (! empty($events)) {
                $event = $events[0];

                $task = new Task();
                $task->setHeaders((array) $headers);
                $task->setQueryString($event->data);
                $task->setDatetime($event->time);
                $task->setId($event->getId());
                $task->setMethod($event->tipe);
            } // if event
        } // if batch

        return $task;
    }

    public function delete(Task $task)
    {
        $batch = $this->queue->next_batch();
        if($batch === null) {
            return;
        }//if batch is null
        $events = $this->queue->get_batch_events($batch->getId());
        if(empty($events)) {
            return;
        }//if empty events
        foreach($events as $event) {
            if($event->getId() == $task->getId()) {
                $this->queue->event_failed($batch->getId(), $event);
        $this->queue->failed_event_delete($event->getId());
        break;
            }//if found
        }//foreach event
    }
}