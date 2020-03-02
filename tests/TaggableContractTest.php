<?php

namespace Conner\Tests\Tagging;

use Conner\Tagging\Contracts\TaggableContract;
use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;

class TaggableContractBaseTest extends BaseTestCase
{
    public function test_is_implemented_by_taggable_trait()
    {
        $implementation = new TraitImplementation();

        $this->assertNotNull($implementation);
    }
}

class TraitImplementation extends Model implements TaggableContract
{
    use Taggable;
}
