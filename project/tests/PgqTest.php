<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Queue\Queue\Pgq\Queue;
use Queue\Queue\Adapter\PgqAdapter;
use Queue\Queue\Task;
use Exception;

class PgqTest extends TestCase
{

    public function testLifeSicle()
    {
        $name = '/Queue/Queue/Pgq/Queue';
        $src_constr = 'host=' . getenv('PG_HOST') . ' port=' . getenv('PG_PORT') . ' dbname=' . getenv('PG_DATABASE') . ' user=' . getenv('PG_USER') . ' password=' . getenv('PG_PASSWORD');
        
        $queue = new Queue('cname', 'qname', 2, [
            $name,
            "start"
        ], $src_constr);
        $this->assertNotNull($queue, 'queue is null');
        $queue->register();
        if(!$queue->queue_exists()) {
        $queue->create_queue();
        }//if not exists
        
        $adapter = new PgqAdapter($queue);
        $this->assertNotNull($adapter, 'pgqAdapter is null');
        
        $task = new Task();
        $task->setId(1);
                $task->setDatetime(date('Y-m-d H:i:s'));
        $task->setQueryString(['key'=>'value']);
        $task->setMethod('create');
        $adapter->save($task);
        /*
        $nextTask = $adapter->get();
        $this->assertNotNull($nextTask, 'nextTask is null');
        $this->assertTrue($nextTask->getId() == $task->getId(), 'task ids not equals');
        */
        $adapter->complete($task);
        
        $adapter->delete($task);
    }

}