<?php namespace Rtconner\Tagging;

use Illuminate\Support\Str;

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
			$this->slug = Str::slug($this->name);
			$this->name = Str::title($this->name);
			parent::save($options);
		} else {
			throw new \Exception('Tag Name is required');
		}
	}
	
	/**
	 * Name auto-mutator
	 */
	public function setNameAttribute($value) {
		$this->attributes['name'] = Str::title($value);
	}
	
	/**
	 * Increment count of tag by one. This function will create tag record if it does not exist.
	 * 
	 * @param string $tagString
	 */
	public static function incrementCount($tagString, $tagSlug, $count) {
		if($count <= 0) { return; }
		
		$tag = static::where('slug', '=', $tagSlug)->first();

		if(!$tag) {
			$tag = new Tag;
			$tag->name = $tagString;
			$tag->slug = $tagSlug;
			$tag->suggest = false;
			$tag->save();
		}
		
		$tag->count = $tag->count + $count;
		$tag->save();
	}
	
	/**
	 * Increment count of tag by one. This function will create tag record if it does not exist.
	 *
	 * @param string $tagString
	 */
	public static function decrementCount($tagString, $tagSlug, $count) {
		if($count <= 0) { return; }
		
		$tag = static::where('slug', '=', $tagSlug)->first();
	
		if($tag) {
			$tag->count = $tag->count - $count;
			$tag->save();
		}
	}
	
}