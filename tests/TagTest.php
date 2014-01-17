<?php namespace Conner\Tagging\Tests;

class TagTest extends TestCase {

	public function testCreate() {
		$tag = new Tag;
		$tag->name = 'Some Tag';
		$tag->save();
		
		$this->assertInternalType('string', $tag->slug);
		$this->assertInternalType('int', $tag->id);
	}
	
}