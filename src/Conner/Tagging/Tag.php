<?php namespace Conner\Tagging;

use Conner\Tagging\TaggingUtil;

class Tag extends \Eloquent {

	protected $table = 'tagging_tags';
	public $timestamps = false;
	protected $softDelete = false;
	public $fillable = ['name'];
	
	public function save(array $options = array()) {
		$validator = \Validator::make(
			array('name' => $this->name),
			array('name' => 'required|min:1')
		);
		
		if($validator->passes()) {
			$this->slug = TaggingUtil::slug($this->name);
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
		$this->attributes['name'] = call_user_func('\Str::title', $value);
	}
	
}
