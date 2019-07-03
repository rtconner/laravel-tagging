<?php

namespace Conner\Tagging\Contracts;

/**
 * Interface of utility functions to help with various tagging functionality.
 *
 * @copyright Copyright (C) 2015 Robert Conner
 * @author Robert Conner <rtconner+gh@gmail.com>
 */
interface TaggingUtility
{
    /**
     * Converts input into array
     *
     * @param string|array $tagNames
     * @return array
     */
    public function makeTagArray($tagNames);

    /**
     * Normalize a tag string
     *
     * @param string $string
     * @return string
     */
    public static function normalize($string);

    /**
     * Create a web friendly URL slug from a string
     *
     * @param string $string
     * @return string
     */
    public static function slug($string);

    /**
     * Private! Please do not call this function directly, just let the Tag library use it.
     * Increment count of tag by one. This function will create tag record if it does not exist.
     *
     * @param string $tagString
     * @param string $tagSlug
     * @param integer $count
     */
    public function incrementCount($tagString, $tagSlug, $count);

    /**
     * Private! Please do not call this function directly, let the Tag library use it.
     * Decrement count of tag by one. This function will create tag record if it does not exist.
     *
     * @param string $tagString
     * @param string $tagSlug
     * @param integer $count
     */
    public function decrementCount($tagString, $tagSlug, $count);

    /**
     * Look at the tags table and delete any tags that are no longer in use by any taggable database rows.
     * Does not delete tags where 'suggest' is true
     *
     * @return int
     */
    public function deleteUnusedTags();

    /**
     * Return string with full namespace of the Tag model
     *
     * @return string
     */
    public static function tagModelString();
}
