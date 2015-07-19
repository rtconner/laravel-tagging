<?php namespace Conner\Tagging;

use Conner\Tagging\TaggingUtil;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Copyright (C) 2014 Robert Conner
 */
class Tag extends Eloquent {

	protected $table = 'tagging_tags';
	public $timestamps = false;
	protected $softDelete = false;
	public $fillable = ['name'];
	
	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		
		if($connection = config('tagging.connection')) {
			$this->connection = $connection;
		}
	}
	
	public function save(array $options = array())
	{
		$validator = \Validator::make(
			array('name' => $this->name),
			array('name' => 'required|min:1')
		);
		
		if($validator->passes()) {
			$normalizer = config('tagging.normalizer');
			$normalizer = empty($normalizer) ? '\Conner\Tagging\TaggingUtil::slug' : $normalizer;
			
			$this->slug = call_user_func($normalizer, $this->name);
			return parent::save($options);
		} else {
			throw new \Exception('Tag Name is required');
		}
	}
	
	/**
	 * Get suggested tags
	 */
	public function scopeSuggested($query)
	{
		return $query->where('suggest', true);
	}
	
	/**
	 * Name auto-mutator
	 */
	public function setNameAttribute($value)
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
		return (new static)->newQuery()
				->where('count', '=', 0)
				->where('suggest', false)
				->delete();
	}
	
}
