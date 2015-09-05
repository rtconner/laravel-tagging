<?php namespace Conner\Tagging\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Copyright (C) 2014 Robert Conner
 */
class Tagged extends Eloquent {

	protected $table = 'tagging_tagged';
	public $timestamps = false;
	protected $fillable = ['tag_name', 'tag_slug'];

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
		return $this->belongsTo('Conner\Tagging\Model\Tag', 'tag_slug', 'slug');
	}

}