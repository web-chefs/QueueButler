<?php

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\Contracts\AbstractBatchRunner;

// Framework
use Illuminate\Queue\WorkerOptions;

class BatchRunner extends AbstractBatchRunner
{

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
        $this->daemon($connectionName, $queue, $options->workerOptions);
    }

    /**
     * Determine if the batch should process on this iteration.
     *
     * @return bool
     */
    protected function daemonShouldRun(WorkerOptions $options)
    {
        $this->checkLimits();
        return parent::daemonShouldRun($options);
    }

    /**
     * Raise the after queue job event.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseAfterJobEvent($connectionName, $job)
    {
        $this->jobCount++;
        parent::raiseAfterJobEvent($connectionName, $job);
        $this->checkLimits();
    }

}
