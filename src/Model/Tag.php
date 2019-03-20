<?php namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Copyright (C) 2014 Robert Conner
 * @package Conner\Tagging\Model
 * @property string id
 * @property string name
 * @property string slug
 * @property bool suggest
 * @property integer count
 * @property TagGroup group
 */
class Tag extends Eloquent
{
    protected $table = 'tagging_tags';
    public $timestamps = false;
    protected $softDelete = false;
    public $fillable = ['name'];
    protected $taggingUtility;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (function_exists('config') && $connection = config('tagging.connection')) {
            $this->connection = $connection;
        }

        $this->taggingUtility = app(TaggingUtility::class);
    }

    public function save(array $options = [])
    {
        if(strlen($this->name) < 1) {
            throw new \RuntimeException('Cannot save a tag with an empty name');
        }

        $normalizer = config('tagging.normalizer');
        $normalizer = $normalizer ?: [$this->taggingUtility, 'slug'];

        $this->slug = call_user_func($normalizer, $this->name);
        return parent::save($options);
    }

    /**
     * Tag group setter
     * @param string $groupName
     * @return Tag
     */
    public function setGroup($groupName)
    {
        $tagGroup = TagGroup::where('slug', $this->taggingUtility->slug($groupName))->first();

        if ($tagGroup) {
            $this->group()->associate($tagGroup);
            $this->save();

            return $this;
        } else {
            throw new \RuntimeException('No Tag Group found: '. $groupName);
        }
    }

    /**
     * Tag group remove
     * @param string $groupName
     * @return Tag
     */
    public function removeGroup(string $groupName)
    {
        $tagGroup = TagGroup::query()->where('slug', $this->taggingUtility->slug($groupName))->first();

        if ($tagGroup) {
            $this->group()->dissociate($tagGroup);
            $this->save();

            return $this;
        } else {
            throw new \RuntimeException('No Tag Group found: '. $groupName);
        }
    }

    /**
     * Tag group helper function
     * @param string $groupName
     * @return bool
     */
    public function isInGroup($groupName): bool
    {
        if ($this->group && ($this->group->slug == $this->taggingUtility->slug($groupName))) {
            return true;
        }
        return false;
    }

    /**
     * Tag group relationship
     */
    public function group()
    {
        return $this->belongsTo(TagGroup::class, 'tag_group_id');
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
     * @return
     */
    public function scopeInGroup(Builder $query, $groupName)
    {
        $groupSlug = $this->taggingUtility->slug($groupName);

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
        $displayer = config('tagging.displayer');
        $displayer = empty($displayer) ? '\Illuminate\Support\Str::title' : $displayer;

        $this->attributes['name'] = call_user_func($displayer, $value);
    }

    /**
     * Look at the tags table and delete any tags that are no londer in use by any taggable database rows.
     * Does not delete tags where 'suggest'value is true
     *
     * @return int
     */
    public static function deleteUnused()
    {
        return (new static )->newQuery()
            ->where('count', '=', 0)
            ->where('suggest', false)
            ->delete();
    }
}
