<?php

use Conner\Tagging\Util;

class UtilTest extends TestCase
{
	public function test_make_tag_array()
	{
		$util = new Util;
		$tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');
		$first = $util->makeTagArray($tagStrings);
		$second = $util->makeTagArray(implode(', ', $tagStrings));
		
		$this->assertEquals($tagStrings, $first);
		$this->assertEquals($tagStrings, $second);

		$result = $util->makeTagArray([1=>'tag1', 3=>'tag2']);
		$this->assertEquals(['tag1', 'tag2'], $result);

		$result = $util->makeTagArray([1=>'tag1']);
		$this->assertEquals(['tag1'], $result);
	}

	public function test_make_tag_array_single()
	{
		$util = new Util;
		$tagStrings = ['tag'];

		$result = $util->makeTagArray($tagStrings);

		$this->assertEquals($result, ['tag']);
	}

	public function test_make_tag_array_from_strings()
	{
		$util = new Util;

		$result = $util->makeTagArray('tag');
		$this->assertEquals($result, ['tag']);

		$result = $util->makeTagArray('tag1,tag2');
		$this->assertEquals($result, ['tag1', 'tag2']);

		$result = $util->makeTagArray('One, Two, Three');
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