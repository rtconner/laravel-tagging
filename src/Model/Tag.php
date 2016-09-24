<?php namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Copyright (C) 2014 Robert Conner
 */
class Tag extends Eloquent
{
    public $timestamps = false;
    public $fillable = ['name'];
    protected $table = 'tagging_tags';
    protected $softDelete = false;
    protected $taggingUtility;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $connection = config('tagging.connection', null);
        if (!is_null($connection)) {
            $this->connection = $connection;
        }

        $this->taggingUtility = app(TaggingUtility::class);
    }

    /**
     * Look at the tags table and delete any tags that are no longer in use by any taggable database rows.
     * Does not delete tags where 'suggest' value is true
     *
     * @return int
     */
    public static function deleteUnused()
    {
        return (new static)->newQuery()
            ->where('count', '=', 0)
            ->where('suggest', false)
            ->delete();
    }

    /**
     * Tag group setter
     *
     * @param $group_name
     * @return $this
     * @throws \Exception
     */
    public function setGroup($group_name)
    {
        $tagGroup = TagGroup::where('slug', $this->taggingUtility->slug($group_name))->first();

        if ($tagGroup) {
            $this->group()->associate($tagGroup);
            $this->save();

            return $this;
        } else {
            throw new \Exception('No Tag Group found');
        }
    }

    /**
     * Tag group relationship
     */
    public function group()
    {
        return $this->belongsTo('\Conner\Tagging\Model\TagGroup', 'tag_group_id');
    }


    /**
     * Save the model to the database.
     *
     * @param  array $options
     * @return bool
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        $validator = app('validator')->make(
            ['name' => $this->name],
            ['name' => 'required|min:1']
        );

        if ($validator->passes()) {
            $normalizer = config('tagging.normalizer', '\Conner\Tagging\Util::slug');

            $this->slug = call_user_func($normalizer, $this->name);
            return parent::save($options);
        } else {
            throw new \Exception('Tag Name is required');
        }
    }

    /**
     * Tag group remove
     *
     * @param $group_name
     * @return $this
     * @throws \Exception
     */
    public function removeGroup($group_name)
    {
        $tagGroup = TagGroup::where('slug', $this->taggingUtility->slug($group_name))->first();

        if ($tagGroup) {
            $this->group()->dissociate($tagGroup);
            $this->save();

            return $this;
        } else {
            throw new \Exception('No Tag Group found');
        }
    }

    /**
     * Tag group helper function
     *
     * @param $group_name
     * @return bool
     */
    public function isInGroup($group_name)
    {
        if ($this->group && ($this->group->slug == $this->taggingUtility->slug($group_name))) {
            return true;
        }
        return false;
    }

    /**
     * Get suggested tags
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return
     */
    public function scopeSuggested($query)
    {
        return $query->where('suggest', true);
    }

    /**
     * Get suggested tags
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param $group_name
     * @return
     */
    public function scopeInGroup($query, $group_name)
    {
        $group_slug = $this->taggingUtility->slug($group_name);

        return $query->whereHas('group', function ($query) use ($group_slug) {
            $query->where('slug', $group_slug);
        });
    }

    /**
     * Set the name of the tag : $tag->name = 'name';
     *
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        $displayer = config('tagging.displayer', '\Illuminate\Support\Str::title');

        $this->attributes['name'] = call_user_func($displayer, $value);
    }
}
