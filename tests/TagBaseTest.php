<?php

namespace Conner\Tests\Tagging;

use Conner\Tagging\Model\Tag;

class TagBaseTest extends BaseTestCase
{
    public function test_saving_a_tag()
    {
        $tag = new Tag(['name' => 'foobar']);

        $tag->save();

        $this->assertEquals('Foobar', $tag->name);
        $this->assertEquals('foobar', $tag->slug);
    }

    public function test_it_can_have_a_description()
    {
        $description = 'Fooobar test description';
        $tag = new Tag([
            'name' => 'foobar',
            'description' => $description
        ]);

        $tag->save();

        $this->assertEquals('Foobar', $tag->name);
        $this->assertEquals('foobar', $tag->slug);
        $this->assertEquals($description, $tag->description);
    }
}
