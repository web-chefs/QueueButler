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
     * @param  int     $delay
     * @param  int     $memory
     * @param  int     $timeout
     * @param  int     $sleep
     * @param  int     $maxTries
     * @param  bool    $force
     * @param  bool    $stopWhenEmpty
     * @param  integer $timeLimit
     * @param  integer $jobLimit
     *
     * @return void
     */
    public function __construct($delay = 0,
                                $memory = 128,
                                $timeout = 60,
                                $sleep = 3,
                                $maxTries = 1,
                                $force = false,
                                $stopWhenEmpty = false,
                                $timeLimit = 60,
                                $jobLimit = 100)
    {
        parent::__construct((int)$delay,
                            (int)$memory,
                            (int)$timeout,
                            (int)$sleep,
                            (int)$maxTries,
                            (bool)$force,
                            (bool)$stopWhenEmpty);

        $this->timeLimit = (int)$timeLimit;
        $this->jobLimit  = (int)$jobLimit;
    }
}
