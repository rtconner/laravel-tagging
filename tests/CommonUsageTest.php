<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Conner\Tagging\Taggable;

class CommonUsageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        \Schema::create('books', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Eloquent::unguard();
    }

    public function tearDown(): void
    {
        \Schema::drop('books');

        parent::tearDown();
    }

    public function test_tag_call()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->tag('test123');
        $stub->tag('456');
        $stub->tag('third');

        $this->assertSame(['Test123', '456', 'Third'], $stub->tagNames());
    }

    public function test_untag_call()
    {
        $stub = Stub::create(['name'=>'Stub']);

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
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        $stub->tag('first');
        $stub->tag('second');

        $stub->retag('foo, bar, another');

        $this->assertEquals(['foo', 'bar', 'another'], $stub->tagSlugs());
    }

    public function test_tag_names_attribute()
    {
        $stub = Stub::create(['name'=>123, 'tag_names'=>'foo, bar']);

        $stub->save();

        $this->assertEquals(['Foo', 'Bar'], $stub->tagNames());
    }

    public function test_the_tagged_property()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        $stub->tag('first');
        $stub->tag('second');

        $tagged = $stub->tagged;

        $this->assertCount(2, $tagged);
        $this->assertEquals('first', $tagged[0]->tag_slug);
        $this->assertEquals('First', $tagged[0]->tag_name);
    }

    public function test_calling_tagNames_as_a_property()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        $stub->tag('first');
        $stub->tag('second');

        $this->assertArraysEqual(['First', 'Second'], $stub->tagNames);
        $this->assertArraysEqual(['First', 'Second'], $stub->tag_names);
    }

    public function test_get_tags()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        $stub->tag('first');
        $stub->tag('second');

        $this->assertEquals('First', $stub->tags[0]->name);
    }

    public function test_setting_tag_names_array()
    {
        $stub = new Stub();
        $stub->name = 'test';
        $stub->tag_names = ['foo', 'bar'];
        $stub->save();

        $tags = $stub->tags;

        $this->assertCount(2, $tags);
        $this->assertEquals('Foo', $stub->tags[0]->name);
    }

    public function test_tagging_with_empty_tags()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        $tagName = "Japan, Asia, Economy, , , , , ";

        $stub->retag($tagName);

        $this->assertEquals(['Japan', 'Asia', 'Economy'], $stub->tag_names);
    }
}

/**
 * @property string name
 */
class Stub extends Eloquent
{
    use Taggable;

    protected $connection = 'testing';

    public $table = 'books';
}