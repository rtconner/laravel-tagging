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
		$tagNames = $this->makeTagArray($tagNames);
		
		foreach($tagNames as $tagName) {
			$this->addTag($tagName);
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
		$tagNames = $this->makeTagArray($tagNames);
		
		foreach($tagNames as $tagName) {
			$this->removeTag($tagName);
		}
	}
	
	/**
	 * Replace the tags from this model
	 * 
	 * @param $tagName string or array
	 */
	public function retag($tagNames) {
		$tagNames = $this->makeTagArray($tagNames);
		$currentTagNames = $this->tagNames();
		
		$deletions = array_diff($currentTagNames, $tagNames);
		$additions = array_diff($tagNames, $currentTagNames);
		
		foreach($deletions as $tagName) {
			$this->removeTag($tagName);
		}
		foreach($additions as $tagName) {
			$this->addTag($tagName);
		}
	}
	
	/**
	 * Filter model to subset with the given tags
	 * 
	 * @param $tagNames array|string
	 */
	public static function withTags($tagNames) {
		$tagSlugs = $this->makeTagArray($tagNames);
		
		array_walk($tagSlugs, 'Conner\Tagging\Tag::slug', array());

		return static::whereHas('tagged', function($q) use($tagSlugs) {
			$q->whereIn('tag_slug', $tagSlugs);
		});
	}
	
	/**
	 * Converts input into array
	 * 
	 * @param $tagName string or array
	 */
	private function makeTagArray($tagNames) {
		if(is_string($tagNames)) {
			$tagNames = explode(',', $tagNames);
		} elseif(!is_array($tagNames)) {
			$tagNames = array(null);
		}
		return $tagNames;
	}
	
	/**
	 * Adds a single tag
	 * 
	 * @param $tagName string
	 */
	private function addTag($tagName) {
		$tagName = trim($tagName);
		$tagSlug = Tag::slug($tagName);
		
		$previousCount = $this->tagged()->where('tag_slug', '=', $tagSlug)->take(1)->count();
		if($previousCount >= 1) { return; }
		
		$tagged = new Tagged(array(
			'tag_name'=>Str::title($tagName),
			'tag_slug'=>$tagSlug,
		));
		
		$this->tagged()->save($tagged);

		Tag::incrementCount($tagName, $tagSlug, 1);
	}
	
	/**
	 * Removes a single tag
	 * 
	 * @param $tagName string
	 */
	private function removeTag($tagName) {
		$tagName = trim($tagName);
		$tagSlug = Tag::slug($tagName);
		
		if($count = $this->tagged()->where('tag_slug', '=', $tagSlug)->delete()) {
			Tag::decrementCount($tagName, $tagSlug, $count);
		}
	}
}
