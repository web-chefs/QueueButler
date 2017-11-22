<?php

namespace WebChefs\QueueButler\Tests\Concerns;

// Aliases
use DB;

trait TestsQueueDb
{

    /**
     * Our test database connection name.
     *
     * @return string
     */
    protected function queueTestDbName()
    {
        return 'sqlite_mem_testing';
    }

    /**
     * Build a test Db to query to jobs table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function queueTestDbQuery()
    {
        return DB::connection($this->queueTestDbName())->table('jobs');
    }

    /**
     * Set default database ENV to be our in memory sqlite db.
     *
     * @return void
     */
    protected function runTestQueueDbEnv()
    {
        // Set test db as default environment
        putenv('DB_DEFAULT=' . $this->queueTestDbName());
    }

    /**
     * Setup job queues to run form a in memory sqlite database.
     *
     * @return void
     */
    protected function runTestQueueDb()
    {
        // Modify config
        $config = $this->app->make('config');
        print_r($config);
        $this->queueTestDb_dbConfig($config);
        $this->queueTestDb_queueConfig($config);

        // Run our migrations
        $dbPath = $this->app->databasePath();
        $this->app->useDatabasePath($this->testEnvPath);
        $this->artisan('migrate:refresh');
        $this->app->useDatabasePath($dbPath);
    }

    /**
     * Set the database connection to a in memory sqlite database.
     *
     * @param \Illuminate\Config\Repository $config
     */
    protected function queueTestDb_dbConfig($config)
    {
        // Setup test DB
        $config->set('database.connections.' . $this->queueTestDbName(), [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $config->set('database.default', $this->queueTestDbName());
    }

    /**
     * Set the queue driver to be our database.
     *
     * @param \Illuminate\Config\Repository $config
     */
    protected function queueTestDb_queueConfig($config)
    {
        $config->set('queue.connections.' . $this->queueTestDbName(), [
            'driver'      => 'database',
            'table'       => 'jobs',
            'queue'       => 'default',
            'retry_after' => 90,
        ]);
        $config->set('queue.default', $this->queueTestDbName());
    }

}