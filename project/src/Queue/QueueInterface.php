<?php

namespace Queue\Queue;

interface QueueInterface
{
    /**
     * @param Task $task
     */
    public function save(Task $task);

    /**
     * @param Task $task
     */
    public function complete(Task $task);

    /**
     * @return Task|null
     */
    public function get(): ?Task;

    /**
     * @param Task $task
     */
    public function delete(Task $task);
}