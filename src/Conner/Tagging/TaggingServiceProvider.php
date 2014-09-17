<?php namespace Conner\Addresses;

use Illuminate\Support\ServiceProvider;

class AddressesServiceProvider extends ServiceProvider {

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