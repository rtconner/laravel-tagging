<?php

namespace Conner\Tests\Tagging;

use Illuminate\Support\Collection;

class CommonUsageBaseTest extends BaseTestCase
{
    public function test_tag_call()
    {
        $stub = $this->book();

        $stub->tag('test123');
        $stub->tag('456');
        $stub->tag('third');

        $this->assertSame(['Test123', '456', 'Third'], $stub->tagNames());
    }

    public function test_untag_call()
    {
        $stub = $this->book();

        $stub->tag('one');
        $stub->tag('two');
        $stub->tag('three');

        $stub->untag('two');

        $this->assertArraysEqual(['Three', 'One'], $stub->tagNames());

        $stub->untag('ONE');
        $this->assertArraysEqual(['Three'], $stub->tagNames());
    }

    public function test_retag()
    {
        $stub = $this->book();

        $stub->tag('first');
        $stub->tag('second');

        $stub->retag('foo, bar, another');

        $this->assertEquals(['foo', 'bar', 'another'], $stub->tagSlugs());
    }

    public function test_tag_names_attribute()
    {
        $stub = $this->book(['tag_names' => 'foo, bar']);

        $stub->save();

        $this->assertEquals(['Foo', 'Bar'], $stub->tagNames());
    }

    public function test_the_tagged_property()
    {
        $stub = $this->book();

        $stub->tag('first');
        $stub->tag('second');

        $tagged = $stub->tagged;

        $this->assertCount(2, $tagged);
        $this->assertEquals('first', $tagged[0]->tag_slug);
        $this->assertEquals('First', $tagged[0]->tag_name);
    }

    public function test_calling_tagNames_as_a_property()
    {
        $stub = $this->book();

        $stub->tag('first');
        $stub->tag('second');

        $this->assertArraysEqual(['First', 'Second'], $stub->tagNames);
        $this->assertArraysEqual(['First', 'Second'], $stub->tag_names);
    }

    public function test_get_tags()
    {
        $stub = $this->book();

        $stub->tag('first');
        $stub->tag('second');

        $this->assertEquals('First', $stub->tags[0]->name);
    }

    public function test_setting_tag_names_array()
    {
        $stub = $this->book();
        $stub->name = 'test';
        $stub->tag_names = ['foo', 'bar'];
        $stub->save();

        $tags = $stub->tags;

        $this->assertCount(2, $tags);
        $this->assertEquals('Foo', $stub->tags[0]->name);
    }

    public function test_tagging_with_empty_tags()
    {
        $stub = $this->book();

        $tagName = "Japan, Asia, Economy, , , , , ";

        $stub->retag($tagName);

        $this->assertEquals(['Japan', 'Asia', 'Economy'], $stub->tag_names);
    }

    function test_withAllTags()
    {
        $one = $this->book();
        $two = $this->book();
        $three = $this->book();

        $one->tag(['one']);
        $two->tag(['b', 'two']);
        $three->tag(['one', 'two']);

        $list = Book::withAllTags(['one', 'two'])->get();

        $this->assertCount(1, $list);
        $this->assertEquals($three->id, $list[0]->id);
    }

    function test_withAnyTags()
    {
        $one = $this->book();
        $two = $this->book();
        $three = $this->book();

        $one->tag(['one']);
        $two->tag(['b', 'two']);

        /** @var Collection $list */
        $list = Book::withAnyTag(['one', 'two'])->get();

        $this->assertCount(2, $list);
        $indexed = $list->keyBy('id');
        $this->assertNotEmpty($indexed[$one->id]);
        $this->assertNotEmpty($indexed[$two->id]);
    }

    function test_withoutTags()
    {
        $one = $this->book();
        $two = $this->book();
        $three = $this->book();

        $one->tag(['one']);
        $two->tag(['b', 'two']);

        /** @var Collection $list */
        $list = Book::withoutTags(['one', 'two'])->get();

        $this->assertCount(1, $list);
        $this->assertEquals($three->id, $list[0]->id);
    }
}