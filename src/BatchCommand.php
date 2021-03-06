<?php

declare(strict_types=1);

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\BatchOptions;
use WebChefs\QueueButler\Laravel\WorkCommand;
use WebChefs\QueueButler\Laravel\WorkerOptions;

class BatchCommand extends WorkCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'queue:batch
                            {connection? : The name of the queue connection to work}
                            {--queue= : The names of the queues to work}
                            {--stop-when-empty : Stop when the queue is empty}
                            {--delay=0 : The number of seconds to delay failed jobs}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=1 : Number of times to attempt a job before logging it failed}
                            {--time-limit=60 : The max time in seconds the batch should run for}
                            {--job-limit=100 : The maximum number of Jobs that the batch should process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing jobs on the queue as single once off batch';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        if ($this->downForMaintenance() && $this->option('once')) {
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

        return $this->runWorker(
            $connection, $queue
        );
    }

    /**
     * Run the worker instance.
     *
     * @param  string  $connection
     * @param  string  $queue
     *
     * @return integer
     */
    protected function runWorker($connection, $queue)
    {
        $this->worker->setCache($this->laravel['cache']->driver());

        return $this->worker->batch( $connection, $queue, $this->gatherWorkerOptions() );
    }

    /**
     * Gather all of the queue worker options as a single object.
     *
     * @return BatchOptions
     */
    protected function gatherWorkerOptions(): WorkerOptions
    {
        return new BatchOptions(
            $this->option('delay'),
            $this->option('memory'),
            $this->option('timeout'),
            $this->option('sleep'),
            $this->option('tries'),
            $this->option('force'),
            $this->option('stop-when-empty'),
            $this->option('time-limit'),
            $this->option('job-limit')
        );
    }
}
