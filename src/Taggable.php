<?php

namespace Conner\Tagging;

use Conner\Tagging\Events\TagAdded;
use Conner\Tagging\Events\TagRemoved;
use Conner\Tagging\Model\Tag;
use Conner\Tagging\Model\Tagged;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @package Conner\Tagging
 * @method static Builder withAllTags(array $tags)
 * @method static Builder withAnyTag(array $tags)
 * @method static Builder withoutTags(array $tags)
 * @property Collection|Tagged[] tagged
 * @property Collection|Tag[] tags
 * @property string[] tag_names
 */
trait Taggable
{
    /**
     * Temp storage for auto tag
     *
     * @var mixed
     * @access protected
     */
    protected $autoTagValue;

    /**
     * Track if auto tag has been manually set
     *
     * @var boolean
     * @access protected
     */
    protected $autoTagSet = false;

    /**
     * Boot the soft taggable trait for a model.
     *
     * @return void
     */
    public static function bootTaggable()
    {
        if(static::untagOnDelete()) {
            static::deleting(function($model) {
                $model->untag();
            });
        }

        static::saved(function ($model) {
            $model->autoTagPostSave();
        });
    }

    /**
     * Return collection of tagged rows related to the tagged model
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @access private
     */
    public function tagged()
    {
        return $this->morphMany(TaggingUtility::taggedModelString(), 'taggable')
            ->with('tag');
    }

    /**
     * Return collection of tags related to the tagged model
     * TODO : I'm sure there is a faster way to build this, but
     * If anyone knows how to do that, me love you long time.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Tagged[]
     */
    public function getTagsAttribute()
    {
        return $this->tagged->map(function(Tagged $item){
            return $item->tag;
        });
    }

    /**
     * Get the tag names via attribute, example $model->tag_names
     */
    public function getTagNamesAttribute(): array
    {
        return $this->tagNames();
    }

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param string|array $tagNames
     */
    public function addTags($tagNames)
    {
        $tagNames = TaggingUtility::makeTagArray($tagNames);

        foreach($tagNames as $tagName) {
            $this->addSingleTag($tagName);
        }
    }

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param string|array $tagNames
     */
    public function tag($tagNames)
    {
        return $this->addTags($tagNames);
    }

    /**
     * Return array of the tag names related to the current model
     *
     * @return array
     */
    public function tagNames(): array
    {
        return $this->tagged->map(function($item){
            return $item->tag_name;
        })->toArray();
    }

    /**
     * Return array of the tag slugs related to the current model
     *
     * @return array
     */
    public function tagSlugs(): array
    {
        return $this->tagged->map(function($item){
            return $item->tag_slug;
        })->toArray();
    }

    /**
     * Remove the tag from this model
     *
     * @param string|array|null $tagNames (or null to remove all tags)
     */
    public function untag($tagNames = null)
    {
        if(is_null($tagNames)) {
            $tagNames = $this->tagNames();
        }

        $tagNames = TaggingUtility::makeTagArray($tagNames);

        foreach($tagNames as $tagName) {
            $this->removeSingleTag($tagName);
        }

        if(static::shouldDeleteUnused()) {
            TaggingUtility::deleteUnusedTags();
        }
    }

    /**
     * Replace the tags from this model
     *
     * @param string|array $tagNames
     */
    public function retag($tagNames)
    {
        $tagNames = TaggingUtility::makeTagArray($tagNames);
        $currentTagNames = $this->tagNames();

        $deletions = array_diff($currentTagNames, $tagNames);
        $additions = array_diff($tagNames, $currentTagNames);

        $this->untag($deletions);

        foreach($additions as $tagName) {
            $this->addSingleTag($tagName);
        }
    }

    /**
     * Filter model to subset with the given tags
     *
     * @param Builder $query
     * @param array|string $tagNames
     * @return Builder
     * @access private
     */
    public function scopeWithAllTags(Builder $query, $tagNames): Builder
    {
        if(!is_array($tagNames)) {
            $tagNames = func_get_args();
            array_shift($tagNames);
        }

        $tagNames = TaggingUtility::makeTagArray($tagNames);

        $className = $query->getModel()->getMorphClass();

        foreach($tagNames as $tagSlug) {

            $model = TaggingUtility::taggedModelString();

            $tags = $model::query()
                ->where('tag_slug', TaggingUtility::normalize($tagSlug))
                ->where('taggable_type', $className)
                ->get()
                ->pluck('taggable_id')
                ->unique();

            $primaryKey = $this->getKeyName();
            $query->whereIn($this->getTable().'.'.$primaryKey, $tags);
        }

        return $query;
    }

    /**
     * Filter model to subset with the given tags
     *
     * @param Builder $query
     * @param array|string $tagNames
     * @return Builder
     * @access private
     */
    public function scopeWithAnyTag(Builder $query, $tagNames): Builder
    {
        $tags = $this->assembleTagsForScoping($query, $tagNames);

        return $query->whereIn($this->getTable().'.'.$this->getKeyName(), $tags);
    }

    /**
     * Filter model to subset without the given tags
     *
     * @param Builder $query
     * @param array|string $tagNames
     * @return Builder
     * @access private
     */
    public function scopeWithoutTags(Builder $query, $tagNames): Builder
    {
        $tags = $this->assembleTagsForScoping($query, $tagNames);

        return $query->whereNotIn($this->getTable().'.'.$this->getKeyName(), $tags);
    }

    /**
     * Adds a single tag
     *
     * @param string $tagName
     */
    private function addSingleTag($tagName)
    {
        $tagName = trim($tagName);

        if(strlen($tagName) == 0) {
            return;
        }

        $tagSlug = TaggingUtility::normalize($tagName);

        $previousCount = $this->tagged()->where('tag_slug', '=', $tagSlug)->take(1)->count();
        if($previousCount >= 1) { return; }

        $model = TaggingUtility::taggedModelString();

        $tagged = new $model([
            'tag_name' => TaggingUtility::displayize($tagName),
            'tag_slug' => $tagSlug,
        ]);

        $this->tagged()->save($tagged);

        TaggingUtility::incrementCount($tagName, $tagSlug, 1);

        unset($this->relations['tagged']);

        event(new TagAdded($this, $tagSlug, $tagged));
    }

    /**
     * Removes a single tag
     *
     * @param $tagName string
     */
    private function removeSingleTag($tagName)
    {
        $tagName = trim($tagName);

        $tagSlug = TaggingUtility::normalize($tagName);

        if($count = $this->tagged()->where('tag_slug', '=', $tagSlug)->delete()) {
            TaggingUtility::decrementCount($tagName, $tagSlug, $count);
        }

        unset($this->relations['tagged']); // clear the "cache"

        event(new TagRemoved($this, $tagSlug));
    }

    /**
     * Return an array of all of the tags that are in use by this model
     *
     * @return Collection|Tagged[]
     */
    public static function existingTags(): Collection
    {
        $model = TaggingUtility::taggedModelString();

        return $model::query()
            ->distinct()
            ->join('tagging_tags', 'tag_slug', '=', 'tagging_tags.slug')
            ->where('taggable_type', '=', (new static)->getMorphClass())
            ->orderBy('tag_slug', 'ASC')
            ->get(['tag_slug as slug', 'tag_name as name', 'tagging_tags.count as count']);
    }

    /**
     * Return an array of all of the tags that are in use by this model
     * @param array $groups
     * @return Collection|Tagged[]
     */
    public static function existingTagsInGroups($groups): Collection
    {
        $model = TaggingUtility::taggedModelString();

        return $model::query()
            ->distinct()
            ->join('tagging_tags', 'tag_slug', '=', 'tagging_tags.slug')
            ->join('tagging_tag_groups', 'tag_group_id', '=', 'tagging_tag_groups.id')
            ->where('taggable_type', '=', (new static)->getMorphClass())
            ->whereIn('tagging_tag_groups.name', $groups)
            ->orderBy('tag_slug', 'ASC')
            ->get(array('tag_slug as slug', 'tag_name as name', 'tagging_tags.count as count'));
    }


    /**
     * Should untag on delete
     */
    public static function untagOnDelete()
    {
        return isset(static::$untagOnDelete)
            ? static::$untagOnDelete
            : config('tagging.untag_on_delete');
    }

    /**
     * Delete tags that are not used anymore
     */
    public static function shouldDeleteUnused(): bool
    {
        return config('tagging.delete_unused_tags', false);
    }

    /**
     * Set tag names to be set on save
     *
     * @param mixed $value Data for retag
     */
    public function setTagNamesAttribute($value)
    {
        $this->autoTagValue = $value;
        $this->autoTagSet = true;
    }

    /**
     * AutoTag post-save hook
     *
     * Tags model based on data stored in tmp property, or untags if manually
     * set to false value
     */
    public function autoTagPostSave()
    {
        if ($this->autoTagSet) {
            if ($this->autoTagValue) {
                $this->retag($this->autoTagValue);
            } else {
                $this->untag();
            }
        }
    }

    private function assembleTagsForScoping($query, $tagNames)
    {
        if(!is_array($tagNames)) {
            $tagNames = func_get_args();
            array_shift($tagNames);
        }

        $tagNames = TaggingUtility::makeTagArray($tagNames);

        $normalizer = [TaggingUtility::class, 'normalize'];

        $tagNames = array_map($normalizer, $tagNames);
        $className = $query->getModel()->getMorphClass();

        $model = TaggingUtility::taggedModelString();

        $tags = $model::query()
            ->whereIn('tag_slug', $tagNames)
            ->where('taggable_type', $className)
            ->get()
            ->pluck('taggable_id')
            ->unique();

        return $tags;
    }

}
