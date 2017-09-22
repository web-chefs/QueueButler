<?php

namespace WebChefs\QueueButler;

// Exception
use Exception;
use DomainException;

// Package
use WebChefs\QueueButler\BatchRunner;
use WebChefs\QueueButler\BatchCommand;
use WebChefs\QueueButler\Versions\Contracts\IsVersionSmartBatchRunner;

// Framework
use Illuminate\Support\ServiceProvider;

class QueueButlerServiceProvider extends ServiceProvider
{

    protected $commands = [
        BatchCommand::class,
    ];

    /**
     * Register the service provider. Register is called before Boot.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);

        $this->app->bind(IsVersionSmartBatchRunner::class, function ($app) {
            return $this->resolveWorkerVersion($app);
        });
    }

    /**
     * Resolve the relevant BatchRunner template for this laravel version.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     *
     * @return BatchRunner
     */
    protected function resolveWorkerVersion($app)
    {
        $versionParts = explode('.', $app::VERSION);
        list($major, $minor) = $versionParts;

        $className = "WebChefs\QueueButler\Versions\Laravel{$major}_{$minor}BatchCommand";
        if (!class_exists($className)) {
            $className = BatchRunner::class;
        }

        return $app->make($className);
    }

}