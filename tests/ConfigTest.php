<?php

namespace ConnerTests;

use Conner\Tagging\Model\Tag;

class ConfigTest extends TestCase
{
    public function test_delete_unused_tags_true()
    {
        config(['tagging.delete_unused_tags'=>true]);
        $book = $this->book();

        $book->tag(['tag1', 'tag2', 'tag3']);
        $book->untag();

        $this->assertEmpty(Tag::all());
    }

    public function test_delete_unused_tags_false()
    {
        config(['tagging.delete_unused_tags'=>false]);
        $book = $this->book();

        $book->tag(['tag1', 'tag2', 'tag3']);
        $book->untag();

        $this->assertCount(3, Tag::all());
    }
}