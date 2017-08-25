# QueueButler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Laravel Artisan commands that make it easy to run job queues using the Scheduler without the need for installing for running the Queue Daemon or installing Supervisor.

This is ideal for shared hosting or situations where you are not fully in control of the services or management of your hosting infrastructure and all you have access to is a Cron.

## Coding Standards


## Versions

Developed and tested on Laravel 5.4 using PHP 5.6. Should work on older versions, if you successfully test the package on a older version of Laravel and PHP, please let us know using the issue tracker and we will give you credit and list it here.

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practises by being named the following.

```
config/
src/
tests/
vendor/
```

## Install

Via Composer

``` bash
$ composer require web-chefs/QueueButler
```

## Usage

``` php

```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

All code submissions will only be evaluated and accepted as pull-requests. If you have any questions or find any bugs please feel free to open an issue.

## Credits

- [Justin Fossey][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/JFossey/QueueButler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/JFossey/QueueButler/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/JFossey/QueueButler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/JFossey/QueueButler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/JFossey/QueueButler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/JFossey/QueueButler
[link-travis]: https://travis-ci.org/JFossey/QueueButler
[link-scrutinizer]: https://scrutinizer-ci.com/g/JFossey/QueueButler/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/JFossey/QueueButler
[link-downloads]: https://packagist.org/packages/JFossey/QueueButler
[link-author]: https://github.com/JFossey
[link-contributors]: ../../contributors
