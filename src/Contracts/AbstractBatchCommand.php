<?php

namespace WebChefs\QueueButler\Contracts;

// Package
use WebChefs\QueueButler\BatchOptions;
use WebChefs\QueueButler\Contracts\IsVersionSmartBatchRunner;

// Framework
use Illuminate\Queue\Console\WorkCommand;

abstract class AbstractBatchCommand extends WorkCommand {

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
    public function __construct(IsVersionSmartBatchRunner $worker)
    {
        // Run upstream constructors
        parent::__construct($worker);
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