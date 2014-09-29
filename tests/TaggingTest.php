<?php

use Conner\Tagging\Tag;
use Conner\Tagging\TaggingUtil;
use Illuminate\Support\Facades\Config;
use Conner\Tagging\Tests\TaggingStub;

class TaggingTest extends \Orchestra\Testbench\TestCase {

	/**
	 * Define environment setup.
	 *
	 * @param Illuminate\Foundation\Application $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app) {
		// reset base path to point to our package's src directory
		$app['path.base'] = __DIR__ . '/../src';
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		));
	}
	
	public function setUp() {
		parent::setUp();
		
		$artisan = $this->app->make('artisan');
		
		$artisan->call('migrate', array(
			'--database' => 'testbench',
			'--package'=>'rtconner\laravel-tagging',
			'--path'=>'migrations',
		));

		$artisan->call('migrate', array(
			'--database' => 'testbench',
			'--package'=>'rtconner\laravel-tagging',
			'--path'=>'../tests/migrations',
		));
		
		include_once(dirname(__FILE__).'/Stub.php');
	}

	public function testTagging() {
		$stub = $this->randomStub();
	
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');
	
		foreach($tagStrings as $tagString) {
			$stub->tag($tagString);
		}
	
		$this->assertEquals(count($tagStrings)-2, $stub->tagged->count());
	}
	
	public function testUntag() {
		$stub = $this->randomStub();
	
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'TagX', 'TagY', 'TagZ');
	
		foreach($tagStrings as $tagString) {
			$stub->tag($tagString);
		}
	
		$stub->untag($tagStrings[0]);
		$stub->untag($tagStrings[1]);
		$stub->untag($tagStrings[2]);
		$stub->untag($tagStrings[3]);
	
		$this->assertEquals(count($tagStrings)-4, $stub->tagged->count());
	}
	
	public function testCreate() {
		$tag = new Tag;
		$tag->name = 'Some Tag';
		$tag->save();
		
		$this->assertInternalType('string', $tag->slug);
		$this->assertInternalType('int', $tag->id);
	}
	
	public function testTagNames() {
		$stub = $this->randomStub();
		
		$str = 'First Tag, Second Tag, Tag #3';
		$arr = array('First Tag', 'Second Tag', 'Tag #3');
		
		$this->assertSame($arr, TaggingUtil::makeTagArray($str));

		$stub->tag($str);
		$this->assertSame($arr, $stub->tagNames());
	}
	
	public function testRetag() {
		$stub = $this->randomStub();
		
		$stub->tag(array('First Tag', '2nd Tag', 'Tag #3'));

		$stub->retag('A, B, c');
		
		$this->assertSame(array('A', 'B', 'C'), $stub->tagNames());
		
		$stub->untag();
		$this->assertEmpty($stub->tagNames());
	}

	public function testInternational() {
		$stub = $this->randomStub();
		
		$tagStrings = array('Â«Ï„Î±Ð‘Ð¬â„“ÏƒÂ»', 'ÐŸÐ¸ÑˆÐ¸', 'Ð¦ÐµÐ»ÑƒÑŽ', 'ÐŸÐ§Ñ�Ð¦Ñ‰');
	
		foreach($tagStrings as $tagString) {
			$stub->tag($tagString);
		}
		
		$this->assertNotEquals($stub->tagNames(), $tagStrings); // ok i have no clue how to test these properly
		foreach($stub->tagNames() as $tag) {
			$this->assertNotEmpty($tag);
		}
	}

	public function testWithAny() {
		$stub = $this->randomStub();
	
		$stub->tag(array('One', 'Two', 'Three'));

		$found1 = TaggingStub::withAnyTag(array('One', 'Four'))->count();
		$found2 = TaggingStub::withAnyTag(array('One', 'Two', 'Three'))->count();
		$nofound = TaggingStub::withAnyTag(array('ZZZZZZ'))->count();

		$this->assertGreaterThan(0, $found1);
		$this->assertGreaterThan(0, $found2);
		$this->assertEquals(0, $nofound);
		
		$found = TaggingStub::where(1, 1)->withAnyTag(array('One', 'Two', 'Three'))->count();
		$notfound = TaggingStub::where(1, 1)->withAnyTag(array('ZZZZZZ'))->count();
		
		$this->assertGreaterThan(0, $found);
		$this->assertEquals(0, $notfound);
	}

	public function testWithAll() {
		$stub = $this->randomStub();
	
		$stub->tag(array('One', 'Two', 'Three'));
	
		$nofound = TaggingStub::withAllTags(array('One', 'Four'))->count();
		$found = TaggingStub::withAllTags(array('One', 'Two'))->count();

		$this->assertGreaterThan(0, $found);
		$this->assertEquals(0, $nofound);
		
		$nofound = TaggingStub::where(1, 1)->withAllTags(array('One', 'Four'))->count();
		$found = TaggingStub::where(1, 1)->withAllTags(array('One', 'Two'))->count();
		
		$this->assertGreaterThan(0, $found);
		$this->assertEquals(0, $nofound);
	}
	
	private function randomStub() {
		$stub = new TaggingStub;
		$stub->id = rand(1,1000);
		$stub->save();
		
		return $stub;
	}

}