<?php

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\BatchOptions;

// Framework
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;

class BatchRunner extends Worker
{

    /**
     * @var int
     */
    protected $jobCount;

    /**
     * @var float
     */
    protected $startTime;

    /**
     * @var \Illuminate\Queue\WorkerOptions
     */
    protected $options;

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  BatchOptions  $options
     * @return void
     */
    public function batch($connectionName, $queue, BatchOptions $options)
    {
        $this->validOptions($options);
        $this->startTime = microtime(true);
        $this->jobCount  = 0;
        parent::daemon($connectionName, $queue, $options);
    }

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
        $this->validOptions($options);

        // For daemon to use batch if called by accident.
        $this->batch($connectionName, $queue, $options);
    }

    /**
     * Process the given job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  string  $connectionName
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function runJob($job, $connectionName, WorkerOptions $options)
    {
        $this->validOptions($options);

        parent::runJob($job, $connectionName, $options);
        $this->jobCount++;
    }

    /**
     * Stop the process if necessary.
     *
     * @param  WorkerOptions  $options
     * @param  int  $lastRestart
     */
    protected function stopIfNecessary(WorkerOptions $options, $lastRestart)
    {
        $this->checkLimits();
        parent::stopIfNecessary($options, $lastRestart);
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int   $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        $this->checkLimits();
        parent::sleep($seconds);
    }

    /**
     * Check our batch limits and stop the command if we reach a limit.
     *
     * @param  WorkerOptions $options
     */
    protected function checkLimits()
    {
        if ($this->isTimeLimit($this->options->timeLimit) || $this->isJobLimit($this->options->jobLimit)) {
            $this->stop();
        }
    }

    /**
     * Check if the batch timelimit has been reached.
     *
     * @param  init     $timeLimit
     *
     * @return boolean
     */
    protected function isTimeLimit($timeLimit)
    {
        return (microtime(true) - $this->startTime) > $timeLimit;
    }

    /**
     * Check if the batch job limit has been reached.
     *
     * @param  int        $jobLimit
     *
     * @return boolean
     */
    protected function isJobLimit($jobLimit)
    {
        return $this->jobCount >= $jobLimit;
    }

    /**
     * Use type hinting for validation were we cant change method argument
     * types of parent.
     *
     * @param  BatchOptions $options
     */
    protected function validOptions(BatchOptions $options)
    {
        // Store valid options
        $this->options = $options;
    }

}
