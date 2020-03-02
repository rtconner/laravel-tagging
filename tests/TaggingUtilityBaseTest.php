<?php

namespace Conner\Tests\Tagging;

use Conner\Tagging\Model\Tag;
use Conner\Tagging\TaggingUtility;
use Illuminate\Support\Str;

class TaggingUtilityBaseTest extends BaseTestCase
{
    public function test_make_tag_array()
    {
        $tagStrings = array('First Tag', '2nd Tag', 'Tag #3', 'tag #3', 'TAG #3');
        $first = TaggingUtility::makeTagArray($tagStrings);
        $second = TaggingUtility::makeTagArray(implode(', ', $tagStrings));

        $this->assertEquals($tagStrings, $first);
        $this->assertEquals($tagStrings, $second);

        $result = TaggingUtility::makeTagArray([1=>'tag1', 3=>'tag2']);
        $this->assertEquals(['tag1', 'tag2'], $result);

        $result = TaggingUtility::makeTagArray([1=>'tag1']);
        $this->assertEquals(['tag1'], $result);
    }

    public function test_make_tag_array_single()
    {
        $tagStrings = ['tag'];

        $result = TaggingUtility::makeTagArray($tagStrings);

        $this->assertEquals($result, ['tag']);
    }

    public function test_make_tag_array_from_strings()
    {
        $result = TaggingUtility::makeTagArray('tag');
        $this->assertEquals($result, ['tag']);

        $result = TaggingUtility::makeTagArray('tag1,tag2');
        $this->assertEquals($result, ['tag1', 'tag2']);

        $result = TaggingUtility::makeTagArray('One, Two, Three');
        $this->assertEquals($result, ['One', 'Two', 'Three']);
    }

    public function test_slug()
    {
        $this->assertEquals('sugar-free', TaggingUtility::slug('Sugar Free'));

        $str = 'ÐŸÐ§Ñ�Ð¦Ñ‰';
        $this->assertNotEquals(TaggingUtility::slug($str), $str);

        $str = 'quiÃ©nsÃ­';
        $this->assertNotEquals(TaggingUtility::slug($str), $str);

        $str = 'ÄŒÄ¢';
        $this->assertNotEquals(TaggingUtility::slug($str), $str);

        $str = 'same-slug';
        $this->assertEquals(TaggingUtility::slug($str), $str);

        $str = '&=*!$&&,';
        $this->assertNotEquals(TaggingUtility::slug($str), $str);
    }

    public function test_normalizer()
    {
        $this->assertEquals(TaggingUtility::slug('Sugar Free'), TaggingUtility::normalize('Sugar Free'));
        $this->assertEquals(TaggingUtility::slug('ÐŸÐ§Ñ�Ð¦Ñ‰'), TaggingUtility::normalize('ÐŸÐ§Ñ�Ð¦Ñ‰'));
        $this->assertEquals(TaggingUtility::slug('quiÃ©nsÃ­'), TaggingUtility::normalize('quiÃ©nsÃ­'));

        config(['tagging.normalizer' => function($str) { return 'aaa'; }]);
        $this->assertEquals('aaa', TaggingUtility::normalize('some string'));
    }

    public function test_normalizer_with_old_class()
    {
        $this->assertEquals(TaggingUtility::slug('Sugar Free'), TaggingUtility::normalize('Sugar Free'));
        $this->assertEquals(TaggingUtility::slug('ÐŸÐ§Ñ�Ð¦Ñ‰'), TaggingUtility::normalize('ÐŸÐ§Ñ�Ð¦Ñ‰'));
        $this->assertEquals(TaggingUtility::slug('quiÃ©nsÃ­'), TaggingUtility::normalize('quiÃ©nsÃ­'));

        config(['tagging.normalizer' => '\Conner\Tagging\Util::slug']);
        $this->assertEquals('some-string', TaggingUtility::normalize('some string'));
    }

    public function test_normalizer_with_new_class()
    {
        $this->assertEquals(TaggingUtility::slug('Sugar Free'), TaggingUtility::normalize('Sugar Free'));
        $this->assertEquals(TaggingUtility::slug('ÐŸÐ§Ñ�Ð¦Ñ‰'), TaggingUtility::normalize('ÐŸÐ§Ñ�Ð¦Ñ‰'));
        $this->assertEquals(TaggingUtility::slug('quiÃ©nsÃ­'), TaggingUtility::normalize('quiÃ©nsÃ­'));

        config(['tagging.normalizer' => '\Conner\Tagging\TaggingUtility::slug']);
        $this->assertEquals('some-string', TaggingUtility::normalize('some string'));
    }

    public function test_displayize()
    {
        $this->assertEquals('Sugar Free', TaggingUtility::displayize('sugar free'));
        $this->assertEquals(Str::title('ÐŸÐ§Ñ�Ð¦Ñ‰'), TaggingUtility::displayize('ÐŸÐ§Ñ�Ð¦Ñ‰'));

        config(['tagging.displayer' => function($str) { return 'bbb'; }]);
        $this->assertEquals('bbb', TaggingUtility::displayize('some string'));
    }

    public function test_deleteUnusedTags()
    {
        config(['tagging.delete_unused_tags'=>false]);
        $book = $this->book();

        $book->tag(['tag1', 'tag2', 'tag3']);
        $book->untag();

        TaggingUtility::deleteUnusedTags();

        $this->assertEmpty(Tag::all());
    }
}