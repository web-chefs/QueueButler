<?php

namespace WebChefs\QueueButler;

// Package
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
    }

}