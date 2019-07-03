<?php

namespace Conner\Tagging\Providers;

use Conner\Tagging\Console\Commands\GenerateTagGroup;
use Illuminate\Support\ServiceProvider;

/**
 * Copyright (C) 2014 Robert Conner
 */
class TaggingServiceProvider extends ServiceProvider
{
    protected $commands = [
        GenerateTagGroup::class
    ];

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/tagging.php' => config_path('tagging.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../../migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
