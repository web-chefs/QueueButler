<?php

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\Worker;
use WebChefs\QueueButler\BatchCommand;

// Framework
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;

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
        $this->app->bind(Worker::class, function ($app) {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            return new Worker(
                $app['queue'],
                $app['events'],
                $app[ExceptionHandler::class],
                $isDownForMaintenance
            );
        });

        $this->commands($this->commands);
    }

}