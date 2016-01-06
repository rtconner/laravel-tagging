<?php

use Conner\Tagging\Providers\TaggingServiceProvider;

abstract class TestCase extends Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
	    return [TaggingServiceProvider::class];
	}
	
	public function setUp()
	{
		parent::setUp();
	}
	
}