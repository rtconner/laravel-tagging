<?php

namespace Conner\Tests\Tagging;

use Conner\Tagging\Model\Tagged;

class TaggedBaseTest extends BaseTestCase
{
    public function test_saving_a_tag_model()
    {
        Tagged::unguard();

        $tagged = new Tagged([
            'taggable_id'=>'123',
            'tag_slug'=> 'foo',
            'tag_name'=>'Foo',
            'taggable_type'=>'bar',
        ]);

        $tagged->save();

        $this->assertEquals('Foo', $tagged->tag_name);
        $this->assertEquals('foo', $tagged->tag_slug);

        Tagged::reguard();
    }
}