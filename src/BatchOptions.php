<?php

namespace WebChefs\QueueButler;

// Framework
use Illuminate\Queue\WorkerOptions;

class BatchOptions extends WorkerOptions
{

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
        parent::__construct($delay, $memory, $timeout, $sleep, $maxTries, $force);

        $this->timeLimit = $timeLimit;
        $this->jobLimit  = $jobLimit;
    }

}