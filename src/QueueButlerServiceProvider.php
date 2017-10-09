<?php

namespace WebChefs\QueueButler;

// Exception
use Exception;
use DomainException;

// Package
use WebChefs\QueueButler\BatchRunner;
use WebChefs\QueueButler\BatchCommand;
use WebChefs\QueueButler\Contracts\IsVersionSmartBatchRunner;
use WebChefs\QueueButler\Contracts\IsVersionSmartBatchCommand;

// Framework
use Illuminate\Support\ServiceProvider;

class QueueButlerServiceProvider extends ServiceProvider
{

    protected $commands = [
        IsVersionSmartBatchCommand::class,
    ];

    /**
     * Register the service provider. Register is called before Boot.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);

        $this->app->bind(IsVersionSmartBatchCommand::class, function ($app) {
            return $this->resolveCommandVersion($app);
        });

        $this->app->bind(IsVersionSmartBatchRunner::class, function ($app) {
            return $this->resolveWorkerVersion($app);
        });
    }

    /**
     * Resolve the relevant BatchWorker child class for this laravel version.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     *
     * @return BatchCommand
     */
    protected function resolveCommandVersion($app)
    {
        $versionParts = explode('.', $app::VERSION);
        list($major, $minor) = $versionParts;

        $className = "WebChefs\QueueButler\Versions\Laravel{$major}_{$minor}BatchCommand";
        return $this->resolveWithFallback($app, $className, BatchCommand::class);

    }

    /**
     * Resolve the relevant BatchRunner child class for this Laravel version.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     *
     * @return BatchRunner
     */
    protected function resolveWorkerVersion($app)
    {
        $versionParts = explode('.', $app::VERSION);
        list($major, $minor) = $versionParts;

        $className = "WebChefs\QueueButler\Versions\Laravel{$major}_{$minor}BatchRunner";
        return $this->resolveWithFallback($app, $className, BatchRunner::class);
    }

    /**
     * Resolve for a class, but if it fails use the fallback.
     *
     * WARNING, errors are silently dropped
     */
    protected function resolveWithFallback($app, $class, $fallback)
    {
        // Try first choice
        try {
            if (class_exists($class, true)) {
                return $app->make($class);
            }
        }
        catch(\Exception $e) {
            // Ignore Auto Loader Exceptions
        }

        // Use fallback / second choice
        return $app->make($fallback);
    }

}