<?php

use Conner\Tagging\Util;
use Conner\Tagging\Model\Tag;

class TagTest extends PHPUnit_Framework_TestCase {

	public function test_instantiation()
	{
		$tag = new Tag();
		
		$this->assertInternalType('object', $tag);
	}
	
}