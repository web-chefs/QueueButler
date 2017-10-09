<?php

namespace WebChefs\QueueButler\Versions;

// Package
// use WebChefs\QueueButler\BatchRunner;
use WebChefs\QueueButler\BatchOptions;
use WebChefs\QueueButler\Contracts\AbstractBatchRunner;
// use WebChefs\QueueButler\Contracts\IsVersionSmartBatchRunner;


class Laravel5_2BatchRunner extends AbstractBatchRunner
{

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  int     $delay
     * @param  int     $memory
     * @param  int     $sleep
     * @param  int     $maxTries
     * @return array
     */
    public function daemon($connectionName, $queue = null, $delay = 0, $memory = 128, $sleep = 3, $maxTries = 0)
    {
        parent::daemon($connectionName, $queue);
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
        $this->daemon($connectionName,
                      $queue,
                      $options->delay,
                      $options->memory,
                      $options->sleep,
                      $options->maxTries);
    }

    /**
     * Determine if the batch should process on this iteration.
     *
     * @return bool
     */
    protected function daemonShouldRun()
    {
        $this->checkLimits();
        return parent::daemonShouldRun();
    }

    /**
     * Raise the after queue job event.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseAfterJobEvent($connectionName, Job $job)
    {
        $this->jobCount++;
        parent::raiseAfterJobEvent($connectionName, $job);
        $this->checkLimits();
    }


}