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
}

class Stub extends Eloquent
{
    use Taggable;

    protected $connection = 'testing';

    public $table = 'books';
}