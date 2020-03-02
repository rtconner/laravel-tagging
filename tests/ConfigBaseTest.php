<?php

namespace Conner\Tests\Tagging;

use Conner\Tagging\Model\Tag;

class ConfigBaseTest extends BaseTestCase
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