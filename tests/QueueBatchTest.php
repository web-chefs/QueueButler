<?php

namespace WebChefs\QueueButler\Tests;

// Package
use WebChefs\QueueButler\Tests\TestCase;
use WebChefs\QueueButler\Tests\Jobs\QueueBatchTestJob;

// Framework
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

// Vendor
use Symfony\Component\Process\Process;

// Aliases
use DB;
use Queue;

class QueueBatchTest extends TestCase
{
    use DatabaseMigrations,
        Concerns\TestsQueueDb;

    /**
     * @var string
     */
    protected $answerToken;

    /**
     * Test our queue was setup and is empty.
     *
     * @return void
     */
    public function testQueueSetup()
    {
        $queue = $this->queueTestDbQuery();
        $this->assertEquals($queue->count(), 0);

        // Check our queues are setup and working
        $this->assertEquals(Queue::getName(), $this->queueTestDbName());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBatch()
    {
        // $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $this->setupJobAnswer();

        $queue = $this->queueTestDbQuery();
        dispatch(new QueueBatchTestJob);
        $this->assertEquals(1, $queue->count());

        $job = Queue::pop();

        // Validate its our job
        $this->assertEquals($job->resolveName(), QueueBatchTestJob::class);
        $job->release();


        $this->artisan('queue:batch', ['--job-limit' => 1, '--time-limit' => 1]);
        $this->assertEquals(0, $queue->count());
        $this->assertEquals($this->answerToken, $this->app->bind('QueueBatchRunTestAnswer'));

        // 1. Test for job queue driver
        // 2. Test for job added
        // 3. Test job is processed
    }

    /**
     * Bind Job answer to application.
     *
     * @return void
     */
    protected function setupJobAnswer()
    {
        $this->answerToken = Str::random();
        app()->bind('QueueBatchRunAnswerToken', function($app) {
            return $this->answerToken;
        });
    }

}
