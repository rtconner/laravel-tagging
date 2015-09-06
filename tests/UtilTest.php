<?php

use Conner\Tagging\Util;

class UtilTest extends PHPUnit_Framework_TestCase {

	public function test_make_tag_array()
	{
		$util = new Util;
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');
		$first = $util->makeTagArray($tagStrings);
		$second = $util->makeTagArray(implode(', ', $tagStrings));
		
		$this->assertEquals($tagStrings, $first);
		$this->assertEquals($tagStrings, $second);
	}
	
	public function test_make_tag_array_from_string()
	{
		$util = new Util;
		$tagStrings = array('One, Two, Three');
	
		$result = $util->makeTagArray($tagStrings);
	
		$this->assertEquals($result, ['One', 'Two', 'Three']);
	}
	
	public function test_slug()
	{
		$util = new Util;
		$this->assertEquals('sugar-free', $util->slug('Sugar Free'));

		$str = 'ÐŸÐ§Ñ�Ð¦Ñ‰';
		$this->assertNotEquals($util->slug($str), $str);

		$str = 'quiÃ©nsÃ­';
		$this->assertNotEquals($util->slug($str), $str);

		$str = 'ÄŒÄ¢';
		$this->assertNotEquals($util->slug($str), $str);

		$str = 'same-slug';
		$this->assertEquals($util->slug($str), $str);

		$str = '&=*!$&&,';
		$this->assertNotEquals($util->slug($str), $str);
	}
	
}