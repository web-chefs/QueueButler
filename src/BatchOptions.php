<?php

declare(strict_types=1);

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\Laravel\WorkerOptions;

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
     * @param  string  $name
     * @param  int  $backoff
     * @param  int  $memory
     * @param  int  $timeout
     * @param  int  $sleep
     * @param  int  $maxTries
     * @param  bool  $force
     * @param  bool  $stopWhenEmpty
     * @param integer $timeLimit
     * @param integer $jobLimit
     *
     * @return void
     */
    public function __construct($name = 'default',
                                $backoff = 0,
                                $memory = 128,
                                $timeout = 60,
                                $sleep = 3,
                                $maxTries = 1,
                                $force = false,
                                $stopWhenEmpty = false,
                                $timeLimit = 60,
                                $jobLimit = 100)
    {
        parent::__construct($name, $backoff, $memory, $timeout, $sleep, $maxTries, $force, $stopWhenEmpty);

        $this->timeLimit = $timeLimit;
        $this->jobLimit  = $jobLimit;
    }
}
