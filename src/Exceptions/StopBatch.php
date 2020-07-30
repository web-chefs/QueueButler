<?php

declare(strict_types=1);

namespace WebChefs\QueueButler\Exceptions;

// PHP
use Exception;

class StopBatch extends Exception
{
    // Cleanly handle stopping a batch without resorting to killing the process
}