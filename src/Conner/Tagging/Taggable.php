<?php namespace Conner\Tagging;

use Illuminate\Support\Str;

trait Taggable {

	/**
	 * Return collection of tags related to the tagged model
	 * 
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function tagged() {
		return $this->morphMany('Conner\Tagging\Tagged', 'taggable');
	}
	
	/**
	 * Perform the action of tagging the model with the given string
	 * 
	 * @param $tagName string or array
	 */
	public function tag($tagNames) {

		if(is_array($tagNames)) {
			
		} elseif(is_string($tagNames)) {
			$tagNames = explode(',', $tagNames);
		} else {
			$tagNames = array(null);
		}
		
		foreach($tagNames as $tagName) {
			$tagName = trim($tagName);
	
			$tagSlug = Tag::slug($tagName);
			
			$previousCount = $this->tagged()->where('tag_slug', '=', $tagSlug)->take(1)->count();
			if($previousCount >= 1) { continue; }
			
			$tagged = new Tagged(array(
				'tag_name'=>Str::title($tagName),
				'tag_slug'=>$tagSlug,
			));
			
			$this->tagged()->save($tagged);
	
			Tag::incrementCount($tagName, $tagSlug, 1);
		}
	}
	
	/**
	 * Return array of the tag names related to the current model
	 * 
	 * @return array
	 */
	public function tagNames() {
		$tagNames = array();
		$taggedIterator = $this->tagged()->select(array('tag_name'));

		foreach($taggedIterator->get() as $tagged) {
			$tagNames[] = $tagged->tag_name;
		}
		
		return $tagNames;
	}
	
	/**
	 * Remove the tag from this model
	 * 
	 * @param $tagName string or array
	 */
	public function untag($tagNames) {
		if(is_array($tagNames)) {
				
		} elseif(is_string($tagNames)) {
			$tagNames = explode(',', $tagNames);
		} else {
			$tagNames = array(null);
		}
		
		foreach($tagNames as $tagName) {
			$tagName = trim($tagName);
			$tagSlug = Tag::slug($tagName);
			
			if($count = $this->tagged()->where('tag_slug', '=', $tagSlug)->delete()) {
				Tag::decrementCount($tagName, $tagSlug, $count);
			}
		}
	}
	
	/**
	 * Filter model to subset with the given tags
	 * 
	 * @param $tagNames array|string
	 */
	public static function withTags($tagNames) {
		if(is_array($tagNames)) {
			$tagSlugs = $tagNames;
		} elseif(is_string($tagNames)) {
			$tagSlugs = explode(',', $tagNames);
		} else {
			$tagSlugs = array(null);
		}
		
		array_walk($tagSlugs, 'Conner\Tagging\Tag::slug', array());

		return static::whereHas('tagged', function($q) use($tagSlugs) {
			$q->whereIn('tag_slug', $tagSlugs);
		});
	}
}
