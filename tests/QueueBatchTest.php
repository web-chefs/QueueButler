<?php

declare(strict_types=1);

namespace WebChefs\QueueButler\Tests;

// Package
use WebChefs\LaraAppSpawn\ApplicationResolver;
use WebChefs\QueueButler\QueueButlerServiceProvider;
use WebChefs\QueueButler\Tests\Jobs\QueueBatchTestJob;

// Framework
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\TestCase;

// Aliases
use DB;
use Queue;

class QueueBatchTest extends TestCase
{
    /**
     * Indicates if the console output should not be mocked as this is in
     * integration test.
     *
     * This is only applicable in Laravel 5.7 and above.
     *
     * @var bool
     */
    public $mockConsoleOutput = false;

    /**
     * @var string
     */
    protected $answerToken;

    /**
     * @var string
     */
    protected $connectionName = 'queue_batch_test';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // Build Resolver config
        $config = ApplicationResolver::defaultConfig();
        Arr::set($config, 'database.connection', $this->connectionName);
        Arr::set($config, 'queue.connection', $this->connectionName);

        // Add our service provider to vendor builds
        $callback = function(array $config) {
            $config['providers'][] = QueueButlerServiceProvider::class;
            return $config;
        };
        Arr::set($config, 'callback.vendor_config', $callback);

        // Resolve Application
        $resolver  = ApplicationResolver::makeApp(__DIR__, $config);
        $this->app = $resolver->app();

        // Run our database migrations
        $this->artisan('migrate:refresh', [ '--force' => 1 ]);

        return $this->app;
    }

    /**
     * Build a test Db to query to jobs table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function queueTestDbQuery()
    {
        return DB::table('jobs');
    }

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
        $this->assertEquals(Queue::getName(), $this->connectionName);
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
    public function testBatchSinpleQueueProcessing()
    {
        // $tables = \DB::connection()->getDoctrineSchemaManager()->listTableNames();

        // Setup Job testing Application Bindings
        $this->setupJobAnswerToken();

        // Get a db query to our test jobs table.
        // Test our queue works and our Test job gets push on to the queue
        $queue = $this->queueTestDbQuery();

        // Populated the queue with our test job
        dispatch(new QueueBatchTestJob);
        $this->assertEquals(1, $queue->count());

        // Validate the job is our job
        // By popping the job we inadvertently increment the number of attempts
        $job = Queue::pop();
        $this->assertEquals($job->resolveName(), QueueBatchTestJob::class);

        // Put the job back on the queue for processing
        $job->release();

        // Test Job queue processing using queue:batch
        // Attempts needs to be 2 because we manually popped our job once
        $options = ['--job-limit' => 1, '--time-limit' => 2, '--tries' => 2];
        $this->artisan('queue:batch', $options);
        $this->assertEquals(0, $queue->count());
        $this->assertEquals($this->answerToken, $this->app->make('QueueBatchJobAnswer'));
    }

    /**
     * Test the batch queue processing with a job limit where the batch exits
     * once the job limit is reached leaving jobs in the queue, followed by
     * a second batch that processing the remaining jobs.
     *
     * @return void
     */
    public function testBatchMultiWithJobLimit()
    {
        // Setup Job testing Application Bindings
        $this->setupJobAnswerToken();

        // Get a db query to our test jobs table.
        // Test our queue works and our Test job gets push on to the queue
        $queue = $this->queueTestDbQuery();

        // Create 5 test jobs
        collect(range(1,5))->each(function() {
            dispatch(new QueueBatchTestJob);
        });

        // Check the queue was populated.
        $this->assertEquals(5, $queue->count());

        // Test Job queue processing using queue:batch with job limit
        $this->artisan('queue:batch', ['--job-limit' => 3, '--time-limit' => 5]);

        // Check we have the correct number of jobs left in the queue
        $this->assertEquals(2, $queue->count());

        // Test Job queue processing the remaining jobs
        $this->artisan('queue:batch', ['--job-limit' => 5, '--time-limit' => 5]);

        // Check queue is empty
        $this->assertEquals(0, $queue->count());
    }

    /**
     * Bind Job answer to application container.
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
