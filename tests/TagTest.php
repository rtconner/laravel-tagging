<?php

use Conner\Tagging\Model\Tag;

class TagTest extends TestCase
{
    public function test_saving_a_tag()
    {
        $tag = new Tag(['name' => 'foobar']);

        $tag->save();

        $this->assertEquals('Foobar', $tag->name);
        $this->assertEquals('foobar', $tag->slug);
    }
}