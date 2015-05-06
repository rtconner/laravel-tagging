<?php

use Conner\Tagging\Tag;
use Conner\Tagging\TaggableTrait;
use Conner\Tagging\Tagged;
use Conner\Tagging\TaggingUtil;
use Mockery as m;

class TaggingTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
	{
		m::close();
	}
	
	public function testTag()
	{
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');

		$taggable = m::mock('TaggingStub[addTag]');
		$taggable->shouldReceive('addTag')->times(count($tagStrings))->andReturn(null);
		
		foreach($tagStrings as $tagString) {
			$taggable->tag($tagString);
		}
	}
	
	public function testUntag()
	{
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');

		$taggable = m::mock('TaggingStub[removeTag]');
		$taggable->shouldReceive('removeTag')->times(count($tagStrings))->andReturn(null);
		
		foreach($tagStrings as $tagString) {
			$taggable->untag($tagString);
		}
	}

}

class TaggingStub extends \Illuminate\Database\Eloquent\Model {
	use TaggableTrait;
	
	protected static $untagOnDelete = false;
	
	public function addTag($a){}
	public function removeTag($a){}
}