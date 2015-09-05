<?php

use Conner\Tagging\Model\Tagged;

class TaggedTest extends PHPUnit_Framework_TestCase {

	public function test_instantiation()
	{
		$tagged = new Tagged();
	
		$this->assertInternalType('object', $tagged);
	}
	
}