<?php

namespace WebChefs\QueueButler\Tests;

// PHP
use Exception;
use DomainException;

// Package
use WebChefs\QueueButler\QueueButlerServiceProvider;
use WebChefs\QueueButler\Tests\Concerns\TestsQueueDb;

// Framework
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

abstract class TestCase extends LaravelTestCase
{

    /**
     * @var string
     */
    protected $testEnvPath;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $this->testEnvPath = __DIR__;
        $this->setUpTraitEnv();

        // If we are in a laravel project try and discover the application
        try {
            $this->app = require $this->discoverApp(__DIR__);
        }
        // If we are running in a automated build try and include the
        // application from vendor
        catch(Exception $e) {
            $this->writeBuildConfig(__DIR__);
            $this->app = require $this->getVendorAppPath(__DIR__);
        }

        $this->app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Join an array and base bath correctly as a file system path.
     *
     * @param  string $basePath
     * @param  array  $pathParts
     *
     * @return string
     */
    protected function makePath($basePath, $pathParts)
    {
        return $basePath . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $pathParts);
    }

    /**
     * Return the parts that should lead to the laravel route found in vendor.
     *
     * @return array
     */
    protected function getVendorAppRoot()
    {
        return [
            '..',
            'vendor',
            'laravel',
            'laravel',
        ];
    }

    /**
     * Build a path to boostrap/app.php assuming laravel/laravel is a package
     * under vendor. This will only be the case in automated builds for testing
     * purposes.
     *
     * @param  string $path "__DIR__ . '/vendor/laravel/laravel/bootstrap/app.php'"
     *
     * @return string
     */
    protected function getVendorAppPath($path)
    {
        return $this->makePath($path, array_merge($this->getVendorAppRoot(), ['bootstrap', 'app.php']));
    }

    /**
     * A recursive method that works works backwards through the directory
     * structure until it finds "bootstrap/app.php".
     *
     * This should normally resolve to __DIR__ . '../../boostrap/app.php'
     *
     * @param  string $path
     *
     * @return string
     */
    protected function discoverApp($path)
    {
        $file = $path . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, ['bootstrap', 'app.php']);

        if (file_exists($file)) {
            return $file;
        }

        // Go up a level
        $path = dirname($path);

        // Check if we have reached the end
        if ($path == '.' || $path == DIRECTORY_SEPARATOR) {
            throw new DomainException('Laravel "bootstramp/app.php" could not be discovered.');
        }

        // Try again (recursive)
        return $this->discoverApp($path);
    }

    /**
     * Build the path to config/app.php when laravel is in vendor and we are
     * running in an automated travis build.
     *
     * @return string
     */
    protected function getVendorAppConfig($basePath)
    {
        return $this->makePath($basePath, array_merge($this->getVendorAppRoot(), ['config', 'app.php']));
    }

    /**
     * Include and add our Service Provider to the App config.
     *
     * @param  string $configPath
     *
     * @return array
     */
    protected function buildAppConfig($configPath)
    {
        $config = require($configPath);
        $config['providers'][] = QueueButlerServiceProvider::class;
        return $config;
    }

    /**
     * Update the vendor location of config/app.php include our service provider
     *
     * @param  string $basePath
     *
     * @return void
     */
    protected function writeBuildConfig($basePath)
    {
        $configPath = $this->getVendorAppConfig($basePath);

        if (! is_writable($configPath)) {
            throw new Exception('The config/app.php file must be present and writable.');
        }

        $config = $this->buildAppConfig($configPath);

        file_put_contents($configPath, '<?php return '.var_export($config, true).';');
    }

    /**
     * Let testing helper traits run pre-application booting environmental
     * changes.
     *
     * @return void
     */
    protected function setUpTraitEnv()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[TestsQueueDb::class])) {
            $this->runTestQueueDbEnv();
        }
    }

    /**
     * Boot the testing helper traits.
     *
     * @return void
     */
    protected function setUpTraits()
    {
        echo '===========setUpTraits';
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[TestsQueueDb::class])) {
            $this->runTestQueueDb();
        }

        return parent::setUpTraits();
    }


}
