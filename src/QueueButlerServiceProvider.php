<?php

namespace WebChefs\QueueButler;

// Exception
use Exception;
use DomainException;

// Package
use WebChefs\QueueButler\BatchRunner;
use WebChefs\QueueButler\BatchCommand;

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

        $this->app->bind(BatchRunner::class, function ($app) {
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

        try {
            $className = "WebChefs\QueueButler\Versions\Laravel{$major}_{$minor}BatchCommand";
            $instance = $app->make($className);
        }
        catch(Exception $e) {
            throw new DomainException('It looks like your version of Laravel (' . "{$major}.{$minor}" . ') is not supported by the QueueButler queue:batch command. Please open an issue to request support for your version If there is enough demand for a version will consider it.');
        }

        return $instance;
    }

}