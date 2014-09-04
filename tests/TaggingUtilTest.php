<?php

use Conner\Tagging\TaggingUtil;
use Illuminate\Support\Facades\Config;

class TaggingUtilTest extends PHPUnit_Framework_TestCase {

	public function test_makeTagArray() {
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');
		
		$first = TaggingUtil::makeTagArray($tagStrings);
		$second = TaggingUtil::makeTagArray(implode(', ', $tagStrings));
		
		$this->assertEquals($tagStrings, $first);
		$this->assertEquals($tagStrings, $second);
	}
	
	public function test_slug() {
		$this->assertEquals('sugar-free', TaggingUtil::slug('Sugar Free'));

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
	
}