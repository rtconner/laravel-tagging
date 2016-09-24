<?php namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Copyright (C) 2014 Robert Conner
 */
class Tagged extends Eloquent
{
    public $timestamps = false;
    protected $table = 'tagging_tagged';
    protected $fillable = ['tag_name', 'tag_slug'];
    protected $taggingUtility;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->taggingUtility = app(TaggingUtility::class);
    }

    /**
     * Morph to the tag
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function taggable()
    {
        return $this->morphTo();
    }

    /**
     * Get instance of tag linked to the tagged value
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tag()
    {
        $model = $this->taggingUtility->tagModelString();
        return $this->belongsTo($model, 'tag_slug', 'slug');
    }

}