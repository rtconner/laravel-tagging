<?php

namespace Tests;

use Conner\Tagging\Contracts\TaggableContract;
use Conner\Tagging\Events\TagAdded;
use Conner\Tagging\Events\TagRemoved;
use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Foundation\Testing\WithFaker;

class EventTests extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpFaker();

        \Schema::create('books', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    function test_tag_added()
    {
        \Event::listen(TagAdded::class, function(TagAdded $event){
            $this->assertNotEmpty($event->model);
            $this->assertEquals('test', $event->tagSlug);
            $this->assertEquals('Test', $event->tagged->tag_name);
        });

        $book = $this->book();
        $book->tag('Test');
    }

    function test_tag_removed()
    {
        \Event::listen(TagRemoved::class, function(TagRemoved $event){
            $this->assertNotEmpty($event->model);
            $this->assertEquals('test', $event->tagSlug);
        });

        $book = $this->book();
        $book->tag('Test');

        $book->untag('Test');
    }

    function book($attributes = []): Book
    {
        $attributes = array_merge(['name'=>$this->faker->name], $attributes);

        return Book::create($attributes);
    }
}

/**
 * @property string name
 */
class Book extends Eloquent implements TaggableContract
{
    use Taggable;

    protected $connection = 'testing';
    protected static $unguarded = true;
    public $table = 'books';
}