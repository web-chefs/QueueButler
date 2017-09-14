<?php

namespace WebChefs\QueueButler\Versions;

// Package
use Illuminate\Queue\WorkerOptions;
use WebChefs\QueueButler\BatchRunner;

class Laravel5_3BatchCommand extends BatchRunner
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
        $lastRestart = $this->getTimestampOfLastQueueRestart();

        while (true) {
            $this->registerTimeoutHandler($options);

            if ($this->daemonShouldRun($options)) {
                $this->runNextJob($connectionName, $queue, $options);
            } else {
                $this->sleep($options->sleep);
            }

            if ($this->memoryExceeded($options->memory) ||
                $this->queueShouldRestart($lastRestart)) {
                $this->stop();
            }

            // Inject our limit check
            $this->checkLimits($options);
        }
    }

}