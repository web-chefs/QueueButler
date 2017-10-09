<?php

namespace WebChefs\QueueButler;

class BatchOptions
{

    /*
     |--------------------------------------------------------------------------
     | Taken from Illuminate\Queue\WorkerOptions > 5.3
     |--------------------------------------------------------------------------
     */

    /**
     * The number of seconds before a released job will be available.
     *
     * @var int
     */
    public $delay;

    /**
     * The maximum amount of RAM the worker may consume.
     *
     * @var int
     */
    public $memory;

    /**
     * The maximum number of seconds a child worker may run.
     *
     * @var int
     */
    public $timeout;

    /**
     * The number of seconds to wait in between polling the queue.
     *
     * @var int
     */
    public $sleep;

    /**
     * The maximum amount of times a job may be attempted.
     *
     * @var int
     */
    public $maxTries;

    /**
     * Indicates if the worker should run in maintenance mode.
     *
     * @var bool
     */
    public $force;

    /**
     |--------------------------------------------------------------------------
     */

    /**
     * The maximum number of seconds a batch should run for.
     *
     * @var int
     */
    public $timeLimit;

    /**
     * The maximum number of jobs to process in a batch.
     * @var ini
     */
    public $jobLimit;

    /**
     * For compatibility were are able to conditionally pass Laravels own worker
     * options to itself using BatchOptions
     * @var WorkerOptions
     */
    public $workerOptions;

    /**
     * Create a new worker options instance.
     *
     * @param  int  $delay
     * @param  int  $memory
     * @param  int  $timeout
     * @param  int  $sleep
     * @param  int  $maxTries
     * @param  bool $force
     * @param  int  $timelimit
     * @param  int  $jobLimit
     */
    public function __construct($delay = 0,
                                $memory = 128,
                                $timeout = 60,
                                $sleep = 3,
                                $maxTries = 0,
                                $force = false,
                                $timeLimit = 60,
                                $jobLimit = 100)
    {
        $this->delay    = $delay;
        $this->sleep    = $sleep;
        $this->force    = $force;
        $this->memory   = $memory;
        $this->timeout  = $timeout;
        $this->maxTries = $maxTries;

        // Add our options
        $this->timeLimit = $timeLimit;
        $this->jobLimit  = $jobLimit;

        $workOptions = \Illuminate\Queue\WorkerOptions::class;
        if (class_exists($workOptions, true)) {
            $this->workerOptions = new $workOptions($delay,
                                                    $memory,
                                                    $timeout,
                                                    $sleep,
                                                    $maxTries,
                                                    $force);
        }
    }

}