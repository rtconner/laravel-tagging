<?php namespace Conner\Tagging;

use Illuminate\Support\ServiceProvider;

class TaggingServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 */
	protected $defer = false;
	
	/**
	 * Bootstrap the application events.
	 */
	public function boot() {
		$this->package('rtconner/laravel-tagging', 'tagging');
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

	}

	public function provides() {
		return array('tagging');
	}
	
}