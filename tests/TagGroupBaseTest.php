<?php

namespace Conner\Tests\Tagging;

use Conner\Tagging\Model\Tag;
use Conner\Tagging\Model\TagGroup;
use Conner\Tagging\TaggingUtility;

class TagGroupBaseTest extends BaseTestCase
{
    public function test_create_group()
    {
        $tagGroup = $this->createTagGroup('MyTagGroup');

        $this->assertCount(1, TagGroup::all());
    }

    public function test_tag_group_tags_list()
    {
        $tagGroup = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tagGroup->name);

        $this->assertEquals(1, $tagGroup->tags()->count());
        $this->assertEquals($tagGroup->id, $tag->tag_group_id);
    }

    public function test_add_group_to_tag()
    {
        $tagGroup = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tagGroup->name);

        $this->assertCount(1, Tag::inGroup($tagGroup->name)->get());

        $this->assertTrue($tag->isInGroup('MyTagGroup'));
    }

    public function test_delete_group_from_tag()
    {
        $tagGroup = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tagGroup->name);

        $this->assertCount(1, Tag::inGroup($tagGroup->name)->get());
        $this->assertEquals($tagGroup->id, $tag->tag_group_id);

        $tag->removeGroup();

        $this->assertCount(0, Tag::inGroup($tagGroup->name)->get());

        $this->assertFalse($tag->isInGroup('MyTagGroup'));
    }

    public function test_removeGroup_with_no_group()
    {
        $tag = $this->createTag();

        $tag->removeGroup();

        $this->assertTrue(true); // no exceptions thrown
    }

    public function test_delete_group_tag()
    {
        $tagGroup = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tagGroup->name);

        $tagGroup->delete();

        // unless you refresh the tag, it will still think there is a relationship
        $tag = $tag->fresh();

        $this->assertFalse($tagGroup->exists);

        $this->assertNull($tag->group, 'The group should not exist on the tag after it is deleted');

        $this->assertFalse($tag->isInGroup('MyTagGroup'), 'The tag should not belong to a deleted group');
    }

    private function createTagGroup($name = null): TagGroup
    {
        if(is_null($name)) {
            $name = $this->faker->name;
        }

        $model = TaggingUtility::tagGroupModelString();

        return $model::create([
            'name' => $name
        ]);
    }

    private function createTag($name = 'Test Tag'): Tag
    {
        $tag = new Tag();
        $tag->name = $name;
        $tag->save();

        return $tag;
    }
}