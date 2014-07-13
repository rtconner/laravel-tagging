<?php namespace Conner\Tagging\Tests;

use Conner\Tagging\Taggable;
use Conner\Tagging\Tag;
use Conner\Tagging\TaggingUtil;
use Illuminate\Support\Facades\Config;

class TaggingTest extends \TestCase {

	public function setUp() {
		\Illuminate\Foundation\Testing\TestCase::setUp();
		
		\Artisan::call('migrate', array('--package'=>'rtconner\laravel-tagging'));

		$path = realpath(__DIR__.'/migrations');
		$path = substr($path, strlen(getcwd())+1);
		
		\Artisan::call('migrate', array('--path'=>$path));
		
		include_once(dirname(__FILE__).'\Stub.php');
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
	
	public function testSlugs() {
		$str = 'ÐŸÐ§Ñ�Ð¦Ñ‰';
		$this->assertNotEquals(TaggingUtil::slug($str), $str);

		$str = 'quiÃ©nsÃ­';
		$this->assertNotEquals(TaggingUtil::slug($str), $str);

		$str = 'ÄŒÄ¢';
		$this->assertNotEquals(TaggingUtil::slug($str), $str);

		$str = 'same-slug';
		$this->assertEquals(TaggingUtil::slug($str), $str);

		$str = '&=*!$&&,';
		$this->assertNotEquals(TaggingUtil::slug($str), $str);
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