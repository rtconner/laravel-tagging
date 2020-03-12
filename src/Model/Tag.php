<?php

namespace Conner\Tagging\Model;

use Conner\Tagging\TaggingUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @package Conner\Tagging\Model
 * @property string id
 * @property string name
 * @property string slug
 * @property bool suggest
 * @property integer count
 * @property integer tag_group_id
 * @property TagGroup group
 * @property string description
 * @method static suggested()
 * @method static inGroup(string $group)
 */
class Tag extends Model
{
    protected $table = 'tagging_tags';
    public $timestamps = false;
    public $fillable = ['name', 'description'];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = config('tagging.connection');
    }

    /**
     * @inheritDoc
     */
    public function save(array $options = [])
    {
        if(strlen($this->name) < 1) {
            throw new \RuntimeException('Cannot save a tag with an empty name');
        }

        $this->slug = TaggingUtility::normalize($this->name);

        return parent::save($options);
    }

    /**
     * Tag group setter
     * @param string $group
     * @return Tag
     */
    public function setGroup(string $group)
    {
        $model = TaggingUtility::tagGroupModelString();

        $tagGroup = $model::query()
            ->where('slug', TaggingUtility::normalize($group))
            ->first();

        if ($tagGroup) {
            $this->group()->associate($tagGroup);
            $this->save();

            return $this;
        } else {
            throw new \RuntimeException('No Tag Group found: '. $group);
        }
    }

    /**
     * Tag group remove
     * @return Tag
     */
    public function removeGroup()
    {
        $this->group()->dissociate();
        $this->save();

        return $this;
    }

    /**
     * Tag group helper function
     * @param string $groupName
     * @return bool
     */
    public function isInGroup($groupName): bool
    {
        if ($this->group && ($this->group->slug == TaggingUtility::normalize($groupName))) {
            return true;
        }

        return false;
    }

    /**
     * Tag group relationship
     */
    public function group()
    {
        return $this->belongsTo(TaggingUtility::tagGroupModelString(), 'tag_group_id');
    }

    /**
     * Get suggested tags
     */
    public function scopeSuggested($query)
    {
        return $query->where('suggest', true);
    }

    /**
     * Get suggested tags
     * @param Builder $query
     * @param $groupName
     * @return Builder
     */
    public function scopeInGroup(Builder $query, $groupName)
    {
        $groupSlug = TaggingUtility::normalize($groupName);

        return $query->whereHas('group', function (Builder $query) use ($groupSlug) {
            $query->where('slug', $groupSlug);
        });
    }

    /**
     * Set the name of the tag : $tag->name = 'myname';
     *
     * @param string $value
     */
    public function setNameAttribute(string $value)
    {
        $this->attributes['name'] = TaggingUtility::displayize($value);
    }

    /**
     * Look at the tags table and delete any tags that are no longer in use by any taggable database rows.
     * Does not delete tags where 'suggest' value is true
     *
     * @return mixed
     */
    public static function deleteUnused()
    {
        return (new static)->newQuery()
            ->where('count', '=', 0)
            ->where('suggest', false)
            ->delete();
    }
}
