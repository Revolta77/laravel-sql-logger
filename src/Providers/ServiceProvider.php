<?php

namespace Revolta77\LaravelSqlLogger\Providers;

use Revolta77\LaravelSqlLogger\Config;
use Revolta77\LaravelSqlLogger\Console\ClearDbCommand;
use Revolta77\LaravelSqlLogger\SqlLogger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->config = $this->app->make(Config::class);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // make sure events will be fired in Lumen
        $this->app->make('events');

        // merge config
        $this->mergeConfigFrom($this->configFileLocation(), 'sql_logger');

        // register files to be published
        $this->publishes($this->getPublished());
		$this->loadMigrationsFrom( __DIR__ . '/../../publish/migrations' );

        // if no logging is enabled, we can stop here, nothing more should be done
        if (! $this->shouldLogAnything()) {
            return;
        }

        // create logger class
        $logger = $this->app->make(SqlLogger::class);

		$this->app->bind('command.laravelsqllogger:clear', ClearDbCommand::class);
		$this->commands('command.laravelsqllogger:clear');

        // listen to database queries

        $this->app['db']->listen($this->getListenClosure($logger));
    }

    /**
     * Get files to be published.
     *
     * @return array
     */
    protected function getPublished()
    {
        return [
            $this->configFileLocation() => (function_exists('config_path') ?
                config_path('sql_logger.php') :
                base_path('config/sql_logger.php')),
        ];
    }

    /**
     * Verify whether anything should be logged.
     *
     * @return bool
     */
    protected function shouldLogAnything()
    {
        return $this->config->logAllQueries() || $this->config->logSlowQueries();
    }

    /**
     * Get config file location.
     *
     * @return bool|string
     */
    protected function configFileLocation()
    {
        return realpath(__DIR__ . '/../../publish/config/sql_logger.php');
    }

    /**
     * Get closure that will be used for listening executed database queries.
     *
     * @param SqlLogger $logger
     *
     * @return \Closure
     */
    protected function getListenClosure(SqlLogger $logger)
    {
        return function ($query, $bindings = null, $time = null) use ($logger) {
            $logger->log($query, $bindings, $time);
        };
    }
}
