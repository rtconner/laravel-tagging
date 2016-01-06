<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Conner\Tagging\Taggable;
use Illuminate\Database\Capsule\Manager as DB;

class CommonUsageTest extends TestCase
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
		$stub->tag('456');
		$stub->tag('third');
		
		$this->assertSame(['Test123', '456', 'Third'], $stub->tagNames());
	}
	
	public function test_untag_call()
	{
		$stub = Stub::create(['name'=>'Stub']);
		
		$stub->tag('one');
		$stub->tag('two');
		$stub->tag('three');
		
		$stub->untag('two');
		
		$this->assertArraysEqual(['Three', 'One'], $stub->tagNames());
		
		$stub->untag('ONE');
		$this->assertArraysEqual(['Three'], $stub->tagNames());
	}

	public function test_retag()
	{
		$stub = Stub::create(['name'=>123]);
		
		$stub->tag('first');
		$stub->tag('second');
		
		$stub->retag('foo, bar, another');
		$this->assertEquals(['foo', 'bar', 'another'], $stub->tagSlugs());
	}

	public function test_tag_names_attribute()
	{
		$stub = Stub::create(['name'=>123, 'tag_names'=>'foo, bar']);
		
		$stub->save();
		
		$this->assertEquals(['Foo', 'Bar'], $stub->tagNames());
	}
}

class Stub extends Eloquent
{
	use Taggable;
	
	protected $connection = 'testbench';
	
	public $table = 'books';
}