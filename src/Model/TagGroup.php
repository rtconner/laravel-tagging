<?php namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Copyright (C) 2014 Robert Conner
 */
class TagGroup extends Eloquent
{
    public $timestamps = false;
    public $fillable = ['name'];
    protected $table = 'tagging_tag_groups';
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
     * Get suggested tags
     */
    public function tags()
    {
        $model = $this->taggingUtility->tagModelString();

        return $this->hasMany($model, 'tag_group_id');
    }

    /**
     * sets the slug when setting the group name
     *
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }
}
