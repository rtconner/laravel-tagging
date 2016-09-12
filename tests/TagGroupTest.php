<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Conner\Tagging\Model\Tag;
use Conner\Tagging\Model\TagGroup;

class TagGroupTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Eloquent::unguard();

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/../migrations'),
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    private function createTagGroup($group_name = 'MyTagGroup')
    {
        return TagGroup::create([
            'name' => $group_name
            ]);
        $tag_group->name = $group_name;
        // $tag_group->slug = str_slug($group_name);
        $tag_group->save();

        return $tag_group;
    }

    private function createTag($name = 'Test Tag')
    {
        $tag = new Tag();
        $tag->name = $name;
        $tag->save();

        return $tag;
    }

    public function test_create_group()
    {
        $tag_group = $this->createTagGroup('MyTagGroup');

        $this->assertCount(1, TagGroup::all());
    }

    public function test_tag_group_tags_list()
    {
        $tag_group = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tag_group->name);

        $this->assertEquals(1, $tag_group->tags()->count());
    }

    public function test_add_group_to_tag()
    {
        $tag_group = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tag_group->name);

        $this->assertCount(1, Tag::inGroup($tag_group->name)->get());

        $this->assertTrue($tag->isInGroup('MyTagGroup'));
    }

    public function test_delete_group_from_tag()
    {
        $tag_group = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tag_group->name);

        $this->assertCount(1, Tag::inGroup($tag_group->name)->get());

        $tag->removeGroup($tag_group->name);

        $this->assertCount(0, Tag::inGroup($tag_group->name)->get());

        $this->assertFalse($tag->isInGroup('MyTagGroup'));
    }

    public function test_delete_group_tag()
    {
        $tag_group = $this->createTagGroup('MyTagGroup');

        $tag = $this->createTag();

        $tag->setGroup($tag_group->name);

        $tag_group->delete();

        // unless you refresh the tag, it will still think there is a relationship
        $tag = $tag->fresh();

        $this->assertFalse($tag_group->exists);

        $this->assertNull($tag->group, 'The group should not exist on the tag after it is deleted');

        $this->assertFalse($tag->isInGroup('MyTagGroup'), 'The tag should not belong to a deleted group');
    }
}