<?php

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\BatchRunner;
use WebChefs\QueueButler\BatchOptions;

// Framework
use Illuminate\Queue\Console\WorkCommand;

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
                            {--delay=0 : Amount of time to delay failed jobs}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=0 : Number of times to attempt a job before logging it failed}
                            {--time-limit=60 : The max time in seconds the batch should run for}
                            {--job-limit=100 : The maximum number of Jobs that the batch should process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing jobs on the queue as single once off batch';

    /**
     * Create a new queue listen command.
     *
     * @param  \Illuminate\Queue\Worker  $worker
     * @return void
     */
    public function __construct(BatchRunner $worker)
    {
        parent::__construct($worker);
    }

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

    /**
     * Gather all of the queue worker options as a single object.
     *
     * @return \Illuminate\Queue\WorkerOptions
     */
    protected function gatherWorkerOptions()
    {
        return new BatchOptions(
            $this->option('delay'),
            $this->option('memory'),
            $this->option('timeout'),
            $this->option('sleep'),
            $this->option('tries'),
            $this->option('force'),
            $this->option('time-limit'),
            $this->option('job-limit')
        );
    }

}
