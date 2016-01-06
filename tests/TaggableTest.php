<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Conner\Tagging\Taggable;
use Illuminate\Database\Capsule\Manager as DB;

class TagTraitTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		
		Eloquent::unguard();

		$this->artisan('migrate', [
		    '--database' => 'testbench',
		    '--realpath' => realpath(__DIR__.'/../migrations'),
		]);
	}
	
	protected function getEnvironmentSetUp($app)
	{
	    $app['config']->set('database.default', 'testbench');
	    $app['config']->set('database.connections.testbench', [
	        'driver'   => 'sqlite',
	        'database' => ':memory:',
	        'prefix'   => '',
	    ]);
	    
		\Schema::create('books', function ($table) {
			$table->increments('id');
			$table->string('name');
			$table->timestamps();
		});
	}
	
	public function tearDown()
	{
		\Schema::drop('books');
	}

	public function test_tag_call()
	{
		$stub = Stub::create(['name'=>123]);
		
		$stub->tag('test123');
	}
}

class Stub extends Eloquent
{
	use Taggable;
	
	protected $connection = 'testbench';
	
	public $table = 'books';
}