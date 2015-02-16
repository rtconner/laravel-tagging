<?php namespace Conner\Tagging;

use Illuminate\Support\ServiceProvider;

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
	public function boot() {
		$this->publishes([
			__DIR__.'/../../../config/tagging.php' => config_path('tagging.php'),
			__DIR__.'/../../../migrations/2014_01_07_073615_create_tagged_table.php' => base_path('database/migrations/2014_01_07_073615_create_tagged_table.php'),
			__DIR__.'/../../../migrations/2014_01_07_073615_create_tags_table.php' => base_path('database/migrations/2014_01_07_073615_create_tags_table.php'),
		]);
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
	}

	public function when() {
		return array('artisan.start');
	}
	
}