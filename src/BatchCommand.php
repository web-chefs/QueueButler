<?php

namespace WebChefs\QueueButler;

// Package
// use WebChefs\QueueButler\BatchRunner;
use WebChefs\QueueButler\BatchOptions;
use WebChefs\QueueButler\Contracts\AbstractBatchCommand;
use WebChefs\QueueButler\Contracts\IsVersionSmartBatchRunner;

class BatchCommand extends AbstractBatchCommand
{

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->downForMaintenance()) {
            return $this->worker->sleep($this->option('sleep'));
        }

        // We'll listen to the processed and failed events so we can write information
        // to the console as jobs are processed, which will let the developer watch
        // which jobs are coming through a queue and be informed on its progress.
        $this->listenForEvents();

        $connection = $this->argument('connection')
                        ?: $this->laravel['config']['queue.default'];

        // We need to get the right queue for the connection which is set in the queue
        // configuration file for the application. We will pull it based on the set
        // connection being run for the queue operation currently being executed.
        $queue = $this->getQueue($connection);

        $this->runWorker($connection, $queue);
    }

    /**
     * Run the worker instance.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @return array
     */
    protected function runWorker($connection, $queue)
    {
        $this->worker->setCache($this->laravel['cache']->driver());
        return $this->worker->batch( $connection, $queue, $this->gatherWorkerOptions() );
    }

}
