<?php

declare(strict_types=1);

namespace WebChefs\QueueButler\Laravel;

/*
 |------------------------------------------------------------------------------
 | Copied from "framework/src/Illuminate/Queue/Events/Looping.php" on 2020/07/07
 |------------------------------------------------------------------------------
 |
 */

class Looping
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The queue name.
     *
     * @var string
     */
    public $queue;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct($connectionName, $queue)
    {
        $this->queue = $queue;
        $this->connectionName = $connectionName;
    }
}
