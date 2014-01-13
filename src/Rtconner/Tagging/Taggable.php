<?php namespace Rtconner\Tagging;

use Illuminate\Support\Str;

trait Taggable {

	/**
	 * Return collection of tags related to the tagged model
	 * 
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function tagged() {
		return $this->morphMany('Rtconner\Tagging\Tagged', 'taggable');
	}
	
	/**
	 * Perform the action of tagging the model with the given string
	 * 
	 * @param $tagString string
	 */
	public function tag($tagString) {
		$tagString = trim($tagString);
		if(!strlen($tagString)) { return; }

		$tagSlug = Str::slug($tagString);
		
		$previousCount = $this->tagged()->where('tag_slug', '=', $tagSlug)->take(1)->count();
		if($previousCount >= 1) { return; }
		
		$tagged = new Tagged(array(
			'tag_string'=>Str::title($tagString),
			'tag_slug'=>$tagSlug,
		));
		
		$this->tagged()->save($tagged);

		Tag::incrementCount($tagString, $tagSlug, 1);
	}
	
	/**
	 * Return array of the tag strings related to the models
	 * 
	 * @return array
	 */
	public function tagStrings() {
		$tagStrings = array();
		$taggedIterator = $this->tagged()->select(array('tag_string'));

		foreach($taggedIterator->get() as $tagged) {
			$tagStrings[] = $tagged->tag_string;
		}
		
		return $tagStrings;
	}
	
	/**
	 * Remove the tag from this model
	 * 
	 * @param $tagString string
	 */
	public function untag($tagString) {
		$tagString = trim($tagString);
		$tagSlug = Str::slug($tagString);
		
		$count = $this->tagged()->where('tag_slug', '=', $tagSlug)->delete();
		
		Tag::decrementCount($tagString, $tagSlug, $count);
	}
	
}