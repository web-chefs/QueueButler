# QueueButler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Laravel Artisan commands that make it easy to run job queues using the Scheduler without the need for installing for running the Queue Daemon or installing Supervisor.

This is ideal for shared hosting or situations where you are not fully in control of the services or management of your hosting infrastructure and all you have access to is a Cron.

## Versions Support Matrix

| QueueButler | PHP       | Laravel   |
| :---------: | :-------: | :-------: |
| 1.4         | 5.6 - 7.3 | 5.3 - 5.6 |
| 2.0         | 7.0 - 7.4 | 5.5 - 7.x |

**Note:** The PHP version support corresponds with the Laravel PHP support.

## Install

__Via Composer__

``` bash
$ composer require web-chefs/queue-butler
```

__Add Service Provider to `config/app.php`__

```php
'providers' => [
   // Other Service Providers
   WebChefs\QueueButler\QueueButlerServiceProvider::class,
];
```

## Benefits

* Works on any standard hosting system that supports a cron.
* Resilient to failures and will automatically be restarted by the scheduler.
* Newly deployed code will automatically be updated and run the next time the scheduler runs.
* Can create multiple queue batch commands based on volume and types of jobs.

## Usage

### Artisan command

Run `queue:batch` artisan command, supports many of the same options as `queue:work`. Two additional options `time-limit` in seconds (defaults to 60 seconds) and 'job-limit' (defaults to 100) need to be set based on your Scheduling setup.

**Example:**

Run batch for 4 min 30 seconds or 1000 jobs, then stop.

`artisan queue:batch --time-limit=270 --job-limit=1000`

### Scheduler

In your `App\Console\Kernel.php` in the `schedule()` method add your `queue:batch` commands.

Because job queue processing is a long-running process setting `runInBackground()` is highly recommended, else each `queue:batch` command will hold up all scheduled preceding items set up to run after it.

The Scheduler requires a __Cron__ to be setup. See [Laravel documentation](https://laravel.com/docs/master/scheduling) for details on how the Scheduler works.

**Queue Batch Life-cycle**

When scheduling batch processing of a queue, a command will be scheduled to run at a set interval, for a set amount of time or number of jobs and then exits.

It is not recommended to schedule the timeout and the scheduler interval to be the same amount of time as you are more likely going to overlap where the scheduler is called to start the next batch but the last has not finished.

It is best to consider how long your average job takes to complete and then decide on how close you want the batch end time to be to the next start time.

This period between batch commands where no jobs are being processed is your life-cycle overhead.

**Impact Of Code Deployments**

When a queue batch process is started no new code changes will take effect and you will need to wait for a life-cycle to complete and then on the next run newly deployed code will be run and changes reflect.

Errors are rare but can occur when new code is deployed. This happens when a class that has already been parsed and loaded into memory includes a class that was changed after a deploy. In this situation, the first class is running on the old version and the second class is running on the new version.

This is a very rare situation but if you want to avoid this you can look to time your deploys to take place during the life-cycle overhead window.

**`withoutOverlapping()` and Mutex cache expiry**

It is recommended to not overlap the same queue batch commands by using the `withoutOverlapping` scheduler function.

When using `withoutOverlapping()` a cache Mutex is used to keep track of running jobs. The default cache expiry is 1440 minutes (24 hours).

If your batch process is interrupted the scheduler will ignore the task for the time of the expiry and you will have no jobs processing for 24 hours. The only way to resolve this is to clear the cache or manually remove the batch processes cache entry.

To prevent long-running cache expiries it is advised to match your cache expiry time with your task frequency so when a batch command is interrupted it will be restarted by the scheduler within one scheduled life-cycle.

_Note:_ See Laravel 5.3 example that is different from later versions.

**Basic Example:**

Running a batch every minute for 50 seconds or 100 jobs in the background using `runInBackground()`, then stopping.

To prevent overlapping batches running simultaneously with `withoutOverlapping()` matching the life-cycle time in minutes.

If no jobs are found in the queue sleep for 10 seconds before polling the queue for new jobs.

``` php
$schedule->command('queue:batch --queue=default,something,somethingelse --time-limit=50 --job-limit=100 --sleep=10')
         ->everyMinute()
         ->runInBackground()
         ->withoutOverlapping(1);
```

**Recommended Example:**

For most use cases a 5-minute life-cycle will work well by creating a batch command that runs for 4 min 40 seconds or 1000 jobs, with a maxim of 20 seconds life-cycle  overhead.

``` php
$schedule->command('queue:batch --time-limit=280 --job-limit=1000 --sleep=10')
         ->everyFiveMinutes()
         ->runInBackground()
         ->withoutOverlapping(5);
```

**Multiple Queues Example:**

If your application is processing a larger number of jobs using multiple queues, it is recommended setting up different batch scheduler commands per queue.

``` php
// Low volume queues
$schedule->command('queue:batch --queue=default,something,somethingelse --time-limit=50 --job-limit=100 --sleep=10')
         ->everyMinute()
         ->runInBackground()
         ->withoutOverlapping(1);

// High volume dedicated "notifications" queue
$schedule->command('queue:batch --queue=notifications, --time-limit=175 --job-limit=500 --sleep=2')
         ->everyThreeMinutes()
         ->runInBackground()
         ->withoutOverlapping(1);
```

It is not recommended to create multiple batches to the same queue and rather limit one process per queue. Also, see database driver and deadlocks.

**Laravel 5.3 example**

In Laravel 5.3 we needed to set the `expiresAt` cache expiry mutex directly.

```php
// Create Batch Job Queue Processor Task
$scheduledEvent = $schedule->command('queue:batch --time-limit=280 --job-limit=1000 --sleep=10');

// Match cache expiry with frequency
// Set cache mutex expiry to One min (default is 1440)
$scheduledEvent->expiresAt = 5;
$scheduledEvent->everyFiveMinutes()
               ->withoutOverlapping()
               ->runInBackground();
```

### Database Queue Driver

The Laravel queue driver is a common option when used with Queue Butler. The only downside is that there is a limit to the number of simultaneous queue processing commands a MySQL database can support as each process will be trying to lock the tip of the queue when processing new jobs.

> Error: SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction.

These errors are not serious as jobs will continue to be processed, and the biggest impact will be the speed of job processing as processes wait.

We are not aware of similar issues related to using PostgreSQL.

## Standards

* psr-1
* psr-2
* psr-4

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

All code submissions will only be evaluated and accepted as pull-requests. If you have any questions or find any bugs please feel free to open an issue.

## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/web-chefs/queue-butler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/web-chefs/queue-butler.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/web-chefs/QueueButler/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/web-chefs/queue-butler
[link-travis]: https://travis-ci.org/web-chefs/QueueButler
[link-downloads]: https://packagist.org/packages/web-chefs/queue-butler
[link-author]: https://github.com/JFossey
[link-contributors]: ../../contributors
