<?php namespace Conner\Tagging\Providers;

use Illuminate\Support\ServiceProvider;
use Conner\Tagging\Contracts\TaggingUtility;
use Conner\Tagging\Util;

/**
 * Copyright (C) 2014 Robert Conner
 */
class TaggingServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../config/tagging.php' => config_path('tagging.php'),
			__DIR__.'/../../migrations/2014_01_07_073615_create_tagged_table.php' => $this->app->databasePath().('/migrations/2014_01_07_073615_create_tagged_table.php'),
			__DIR__.'/../../migrations/2014_01_07_073615_create_tags_table.php' => $this->app->databasePath().('/migrations/2014_01_07_073615_create_tags_table.php'),
		]);
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() 
	{
		$this->app->singleton(TaggingUtility::class, function () {
			return new Util;
		});
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Illuminate\Support\ServiceProvider::provides()
	 */
	public function provides()
	{
		return [TaggingUtility::class];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Illuminate\Support\ServiceProvider::when()
	 */
	public function when()
	{
		return array('artisan.start');
	}
	
}
