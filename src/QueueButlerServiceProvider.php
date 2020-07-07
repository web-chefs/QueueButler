<?php

declare(strict_types=1);

namespace WebChefs\QueueButler;

// Package
use WebChefs\QueueButler\Laravel\BatchWorker;
use WebChefs\QueueButler\Laravel\BatchCommand;
use WebChefs\QueueButler\Contracts\QueueButtlerBatchWorkerInterface;

// Framework
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;

class QueueButlerServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
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
        $this->app->bind(QueueButtlerBatchWorkerInterface::class, function ($app) {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            return new BatchWorker(
                $app['queue'],
                $app['events'],
                $app[ExceptionHandler::class],
                $isDownForMaintenance
            );
        });

        $this->commands($this->commands);
    }

}