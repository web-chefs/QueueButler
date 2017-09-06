<?php

namespace WebChefs\QueueButler;

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

        $className = "WebChefs\QueueButler\Versions\Laravel{$major}_{$minor}BatchCommand";
        return $app->make($className);
    }

}