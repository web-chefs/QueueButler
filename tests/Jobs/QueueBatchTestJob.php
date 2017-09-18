<?php

namespace  WebChefs\QueueButler\Tests\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueBatchTestJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app()->bind('QueueBatchJobAnswer', function($app) {
            return $app->make('QueueBatchRunAnswerToken');
        });
    }
}
