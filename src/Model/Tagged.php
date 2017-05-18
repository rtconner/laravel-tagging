<?php namespace Conner\Tagging\Model;

use Conner\Tagging\Contracts\TaggingUtility;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Copyright (C) 2014 Robert Conner
 */
class Tagged extends Eloquent
{
	protected $table = 'tagging_tagged';
	public $timestamps = false;
	protected $fillable = ['tag_name', 'tag_slug'];
	protected $taggingUtility;

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);

		if (function_exists('config') && $connection = config('tagging.connection')) {
			$this->connection = $connection;
		}

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
