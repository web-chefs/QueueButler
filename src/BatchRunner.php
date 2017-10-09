<?php

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\Contracts\AbstractBatchRunner;


class BatchRunner extends AbstractBatchRunner
{

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    public function daemon($connectionName, $queue, WorkerOptions $options)
    {
        $this->validOptions($options);

        // For daemon to use batch if called by accident.
        // $this->batch($connectionName, $queue, $options);
        parent::daemon($connectionName, $queue, $options);
    }

    /**
     * Run parent daemon withe the correct args for Laravel version.
     *
     * @param  string       $connectionName
     * @param  string       $queue
     * @param  BatchOptions $options
     *
     * @return void
     * @throws Exception|StopBatch
     */
    protected function runDaemon($connectionName, $queue, BatchOptions $options)
    {
        $this->daemon($connectionName, $queue, $options);
    }

}
