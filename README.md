# QueueButler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Laravel Artisan commands that make it easy to run job queues using the Scheduler without the need for installing for running the Queue Daemon or installing Supervisor.

This is ideal for shared hosting or situations where you are not fully in control of the services or management of your hosting infrastructure and all you have access to is a Cron.

## Standards

* psr-1
* psr-2
* psr-4

## Versions

Developed and tested on Laravel 5.4 using PHP 5.6. Should work on older versions, if you successfully test the package on a older version of Laravel and PHP, please let us know using the issue tracker and we will give you credit and list it here.

## Install

Via Composer

``` bash
$ composer require web-chefs/queue-butler
```

## Usage

### Artisan command

Run `queue:batch` artisan command, supports many of the same options as `queue:work`. Two additional options `time-limit` in seconds the defaults to 60 seconds and 'job-limit' defaults to 100.

Run bactch for 4 min 30 seconds or 1000 jobs, then stop.

`artisan queue:batch --time-limit=270 --job-limit=1000`

### Scheduler

In your `App\Console\Kernel.php` in the `schedule()` method add your batct commands.

``` php
        $schedule->command('queue:batch --queue=default,something,somethingelse --time-limit=50 --job-limit=100')
                 ->everyMinute()
                 ->withoutOverlapping();
```

* Requires cron to be setup. See Laravel documentation for details.


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

All code submissions will only be evaluated and accepted as pull-requests. If you have any questions or find any bugs please feel free to open an issue.

## Credits

- [Justin Fossey][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/web-chefs/queue-butler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/web-chefs/queue-butler/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/web-chefs/queue-butler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/web-chefs/queue-butler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/web-chefs/queue-butler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/web-chefs/queue-butler
[link-travis]: https://travis-ci.org/web-chefs/queue-butler
[link-scrutinizer]: https://scrutinizer-ci.com/g/web-chefs/queue-butler/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/web-chefs/queue-butler
[link-downloads]: https://packagist.org/packages/web-chefs/queue-butler
[link-author]: https://github.com/JFossey
[link-contributors]: ../../contributors
