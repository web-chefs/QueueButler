<?php

namespace WebChefs\QueueButler\Tests;

// Package
use WebChefs\QueueButler\Tests\Concerns\TestsQueueDb;

// Framework
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

abstract class TestCase extends LaravelTestCase
{

    /**
     * @var string
     */
    protected $testEnvPath;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $this->testEnvPath = __DIR__;
        $this->setUpTraitEnv();

        $this->app = require __DIR__ . '/../../../../bootstrap/app.php';

        $this->app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Let testing helper traits run pre-application booting environmental
     * changes.
     *
     * @return void
     */
    protected function setUpTraitEnv()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[TestsQueueDb::class])) {
            $this->runTestQueueDbEnv();
        }
    }

    /**
     * Boot the testing helper traits.
     *
     * @return void
     */
    protected function setUpTraits()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[TestsQueueDb::class])) {
            $this->runTestQueueDb();
        }

        return parent::setUpTraits();
    }


}
