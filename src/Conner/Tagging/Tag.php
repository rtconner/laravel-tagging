<?php namespace Conner\Tagging;

use Conner\Tagging\TaggingUtil;

class Tag extends \Eloquent {

	protected $table = 'tagging_tags';
	public $timestamps = false;
	protected $softDelete = false;
	public $fillable = ['name'];
	
	public function __construct(array $attributes = array()) {
		parent::__construct($attributes);
		
		if($connection = \Config::get('tagging::connection')) {
			$this->connection = $connection;
		}
	}
	
	public function save(array $options = array()) {
		$validator = \Validator::make(
			array('name' => $this->name),
			array('name' => 'required|min:1')
		);
		
		if($validator->passes()) {
			$normalizer = \Config::get('tagging::normalizer');
			$normalizer = empty($normalizer) ? '\Conner\Tagging\TaggingUtil::slug' : $normalizer;
			
			$this->slug = call_user_func($normalizer, $this->name);
			parent::save($options);
		} else {
			throw new \Exception('Tag Name is required');
		}
	}
	
	/**
	 * Get suggested tags
	 */
	public function scopeSuggested($query) {
		return $query->where('suggest', true);
	}
	
	/**
	 * Name auto-mutator
	 */
	public function setNameAttribute($value) {
		$displayer = \Config::get('tagging::displayer');
		$displayer = empty($displayer) ? '\Str::title' : $displayer;
		
		$this->attributes['name'] = call_user_func($displayer, $value);
	}
	
}
