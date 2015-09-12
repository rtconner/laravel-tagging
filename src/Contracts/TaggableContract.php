<?php namespace Conner\Tagging\Contracts;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;

/**
 * Copyright (C) 2015 Robert Conner
 */
interface TaggableContract {

	/**
	 * Return collection of tagged rows related to the tagged model
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function tagged();

	/**
	 * Perform the action of tagging the model with the given string
	 *
	 * @param $tagName string or array
	 */
	public function tag($tagNames);
	
	/**
	 * Return array of the tag names related to the current model
	 *
	 * @return array
	 */
	public function tagNames();

	/**
	 * Return array of the tag slugs related to the current model
	 *
	 * @return array
	 */
	public function tagSlugs();
	
	/**
	 * Remove the tag from this model
	 *
	 * @param $tagName string or array (or null to remove all tags)
	 */
	public function untag($tagNames=null);
	
	/**
	 * Replace the tags from this model
	 *
	 * @param $tagName string or array
	 */
	public function retag($tagNames);

	/**
	 * Filter model to subset with the given tags
	 *
	 * @param $tagNames array|string
	 */
	public function scopeWithAllTags($query, $tagNames);

	/**
	 * Filter model to subset with the given tags
	 *
	 * @param $tagNames array|string
	 */
	public function scopeWithAnyTag($query, $tagNames);
	
	/**
	 * Return an array of all of the tags that are in use by this model
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function existingTags();
	
	/**
	 * Should untag on delete
	 */
	public static function untagOnDelete();
	
	/**
	 * Delete tags that are not used anymore
	 */
	public static function shouldDeleteUnused();

}
