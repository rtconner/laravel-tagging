<?php namespace Conner\Tagging;

use Illuminate\Support\Str;
use Conner\Tagging\TaggingUtil;

trait TaggableTrait {

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
		$tagNames = TaggingUtil::makeTagArray($tagNames);
		
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
		$tagged = $this->tagged()->get(array('tag_name'));

		foreach($tagged as $tagged) {
			$tagNames[] = $tagged->tag_name;
		}
		
		return $tagNames;
	}
	
	/**
	 * Remove the tag from this model
	 *
	 * @param $tagName string or array (or null to remove all tags)
	 */
	public function untag($tagNames=null) {
		if(is_null($tagNames)) {
			$currentTagNames = $this->tagNames();
			foreach($currentTagNames as $tagName) {
				$this->removeTag($tagName);
			}
			return;
		}
		
		$tagNames = TaggingUtil::makeTagArray($tagNames);
		
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
		$tagNames = TaggingUtil::makeTagArray($tagNames);
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
	public function scopeWithAllTags($query, $tagNames) {
		$tagNames = TaggingUtil::makeTagArray($tagNames);
		
		$tagNames = array_map('\Conner\Tagging\TaggingUtil::slug', $tagNames);

		foreach($tagNames as $tagSlug) {
			$query->whereHas('tagged', function($q) use($tagSlug) {
				$q->where('tag_slug', '=', $tagSlug);
			});
		}
		
		return $query;
	}
		
	/**
	 * Filter model to subset with the given tags
	 *
	 * @param $tagNames array|string
	 */
	public function scopeWithAnyTag($query, $tagNames) {
		$tagNames = TaggingUtil::makeTagArray($tagNames);

		$normalizer = \Config::get('tagging::normalizer');
		$normalizer = empty($normalizer) ? '\Conner\Tagging\TaggingUtil::slug' : $normalizer;
		
		$tagNames = array_map($normalizer, $tagNames);

		return $query->whereHas('tagged', function($q) use($tagNames) {
			$q->whereIn('tag_slug', $tagNames);
		});
	}
	
	/**
	 * Adds a single tag
	 *
	 * @param $tagName string
	 */
	private function addTag($tagName) {
		$tagName = trim($tagName);
		$tagSlug = TaggingUtil::slug($tagName);
		
		$previousCount = $this->tagged()->where('tag_slug', '=', $tagSlug)->take(1)->count();
		if($previousCount >= 1) { return; }
		
		$displayer = \Config::get('tagging::displayer');
		$displayer = empty($displayer) ? '\Str::title' : $displayer;
		
		$tagged = new Tagged(array(
			'tag_name'=>call_user_func($displayer, $tagName),
			'tag_slug'=>$tagSlug,
		));
		
		$this->tagged()->save($tagged);

		TaggingUtil::incrementCount($tagName, $tagSlug, 1);
	}
	
	/**
	 * Removes a single tag
	 *
	 * @param $tagName string
	 */
	private function removeTag($tagName) {
		$tagName = trim($tagName);
		
		$normalizer = \Config::get('tagging::normalizer');
		$normalizer = empty($normalizer) ? '\Conner\Tagging\TaggingUtil::slug' : $normalizer;
		
		$tagSlug = call_user_func($normalizer, $tagName);
		
		if($count = $this->tagged()->where('tag_slug', '=', $tagSlug)->delete()) {
			TaggingUtil::decrementCount($tagName, $tagSlug, $count);
		}
	}
}
