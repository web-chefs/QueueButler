<?php

namespace WebChefs\QueueButler\Versions;

// Package
use WebChefs\QueueButler\Contracts\AbstractBatchCommand;

class Laravel5_2BatchCommand extends AbstractBatchCommand
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

        $queue = $this->option('queue');

        $delay = $this->option('delay');

        // The memory limit is the amount of memory we will allow the script to occupy
        // before killing it and letting a process manager restart it for us, which
        // is to protect us against any memory leaks that will be in the scripts.
        $memory = $this->option('memory');

        $connection = $this->argument('connection');

        $this->runWorker(
            $connection, $queue, $delay, $memory
        );
    }

    /**
     * Run the worker instance.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  int  $delay
     * @param  int  $memory
     * @param  bool  $daemon
     * @return array
     */
    protected function runWorker($connection, $queue, $delay, $memory, $daemon = false)
    {
        $this->worker->setCache($this->laravel['cache']->driver());
        return $this->worker->batch( $connection, $queue, $this->gatherWorkerOptions() );
    }

}