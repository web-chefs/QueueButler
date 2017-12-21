<?php

namespace WebChefs\QueueButler\Tests;

// Package
use WebChefs\QueueButler\Tests\TestCase;
use WebChefs\QueueButler\Tests\Jobs\QueueBatchTestJob;

// Framework
use Illuminate\Support\Str;

// Aliases
// use DB;
use Queue;

class QueueBatchTest extends TestCase
{
    use Concerns\TestsQueueDb;

    /**
     * @var string
     */
    protected $answerToken;

    /**
     * Test our test queue was setup correctly and and is empty.
     *
     * @return void
     */
    public function testQueueSetup()
    {
        $queue = $this->queueTestDbQuery();
        $this->assertEquals($queue->count(), 0);

        // Check our queues are setup is using our in memory database
        $this->assertEquals(Queue::getName(), $this->queueTestDbName());
    }

    /**
     * Test queue:batch with a real job run using sqlite in memory database with
     * a random string defined as a Application binding that the job uses to
     * create its own Application binding response. In this way Application
     * bindings are used to maintain state between the test and the job
     * providing a way to run an assertEquals() bases on a Job's processing.
     *
     * @return void
     */
    public function testBatch()
    {
        $tables = \DB::connection()->getDoctrineSchemaManager()->listTableNames();
        print_r($tables);
        print_r(\DB::connection()->getName());

        // Setup Job testing Application Bindings
        $this->setupJobAnswerToken();

        // Test our queue works and our Test job gets push on to the queue
        $queue = $this->queueTestDbQuery();
        dispatch(new QueueBatchTestJob);
        $this->assertEquals(1, $queue->count());

        // Validate the job is our job
        $job = Queue::pop();
        $this->assertEquals($job->resolveName(), QueueBatchTestJob::class);

        // Put the job back on the queue for processing
        $job->release();

        // Test Job queue processing using queue:batch
        $this->artisan('queue:batch', ['--job-limit' => 1, '--time-limit' => 2]);
        $this->assertEquals(0, $queue->count());
        $this->assertEquals($this->answerToken, $this->app->make('QueueBatchJobAnswer'));
    }

    /**
     * Bind Job answer to application.
     *
     * @return void
     */
    protected function setupJobAnswerToken()
    {
        $this->answerToken = Str::random();
        app()->bind('QueueBatchRunAnswerToken', function($app) {
            return $this->answerToken;
        });
    }

}
