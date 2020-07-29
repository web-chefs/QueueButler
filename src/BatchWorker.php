<?php

declare(strict_types=1);

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\Laravel\Worker;
use WebChefs\QueueButler\Laravel\WorkerOptions;
use WebChefs\QueueButler\Contracts\QueueButtlerBatchWorkerInterface;

// Framework
use Illuminate\Queue\Events\WorkerStopping;

class BatchWorker extends Worker implements QueueButtlerBatchWorkerInterface
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
     * @var null|int
     */
    protected $exitCode = null;

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  WorkerOptions  $options
     *
     * @return integer
     */
    public function batch($connectionName, $queue, WorkerOptions $options)
    {
        $this->options   = $options;
        $this->startTime = microtime(true);
        $this->jobCount  = 0;
        $this->exitCode  = null;

        return $this->batchDaemon($connectionName, $queue, $options);
    }

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     *
     * @return void
     */
    public function batchDaemon($connectionName, $queue, WorkerOptions $options)
    {
        if ($this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }

        $lastRestart = $this->getTimestampOfLastQueueRestart();

        while (true) {
            // Before reserving any jobs, we will make sure this queue is not paused and
            // if it is we will just pause this worker for a given amount of time and
            // make sure we do not need to kill this worker process off completely.
            if (! $this->daemonShouldRun($options, $connectionName, $queue)) {
                $this->pauseWorker($options, $lastRestart);

                continue;
            }

            // First, we will attempt to get the next job off of the queue. We will also
            // register the timeout handler and reset the alarm for this job so it is
            // not stuck in a frozen state forever. Then, we can fire off this job.
            $job = $this->getNextJob(
                $this->manager->connection($connectionName), $queue
            );

            if ($this->supportsAsyncSignals()) {
                $this->registerTimeoutHandler($job, $options);
            }

            // If the daemon should run (not in maintenance mode, etc.), then we can run
            // fire off this job for processing. Otherwise, we will need to sleep the
            // worker so no more jobs are processed until they should be processed.
            if ($job) {
                $this->runJob($job, $connectionName, $options);
            } else {
                $this->sleep($options->sleep);
            }

            if ($this->supportsAsyncSignals()) {
                $this->resetTimeoutHandler();
            }

            // Finally, we will check to see if we have exceeded our memory limits or if
            // the queue should restart based on other indications. If so, we'll stop
            // this worker and let whatever is "monitoring" it restart the process.
            $this->stopIfNecessary($options, $lastRestart, $job);

            // gracefully exit loop and batch if we have a exit code
            if ($this->exitCode !== null) {
                // return exit code
                return $this->exitCode;
            }
        }
    }

    /**
     * Raise the after queue job event.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     *
     * @return void
     */
    protected function raiseAfterJobEvent($connectionName, $job)
    {
        $this->jobCount++;
        parent::raiseAfterJobEvent($connectionName, $job);
        $this->checkLimits();
    }

    /**
     * Stop the process if necessary.
     *
     * @param  WorkerOptions  $options
     * @param  int  $lastRestart
     *
     * @return void
     */
    protected function stopIfNecessary(WorkerOptions $options, $lastRestart, $job = null)
    {
        parent::stopIfNecessary($options, $lastRestart, $job);
        $this->checkLimits();
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @param  int  $status
     *
     * @return void
     */
    public function stop($status = 0)
    {
        $this->events->dispatch(new WorkerStopping($status));

        // Cleanly handle stopping a batch without resorting to killing the process
        // This is required for end to end testing
        $this->exitCode = $status;
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int   $seconds
     *
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
     *
     * @return void
     */
    protected function checkLimits()
    {
        if ($this->isTimeLimit() || $this->isJobLimit()) {
            $this->stop();
        }
    }

    /**
     * Check if the batch timelimit has been reached.
     *
     * @return boolean
     */
    protected function isTimeLimit(): bool
    {
        return (microtime(true) - $this->startTime) > $this->options->timeLimit;
    }

    /**
     * Check if the batch job limit has been reached.
     *
     * @return boolean
     */
    protected function isJobLimit(): bool
    {
        return $this->jobCount >= $this->options->jobLimit;
    }
}
