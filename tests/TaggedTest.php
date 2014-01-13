<?php namespace Rtconner\Tagging\Tests;

use \Mockery as m;

class TaggedTest extends TestCase {

	public function testTagging() {
		$stub = $this->createStub();
		
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');
		
		foreach($tagStrings as $tagString) {
			$stub->tag($tagString);
		}
		
		$this->assertEquals(count($tagStrings)-2, $stub->tagged->count());
	}
	
	public function testUntag() {
		$stub = $this->createStub();
		
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
	
}