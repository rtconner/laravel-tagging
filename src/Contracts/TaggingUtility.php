<?php namespace Conner\Tagging\Contracts;

/**
 * Interface of utility functions to help with various tagging functionality.
 *
 * @author Rob Conner <rtconner+gh@gmail.com>
 *
 * Copyright (C) 2015 Robert Conner
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
     * Create a web friendly URL slug from a string.
     *
     * Although supported, transliteration is discouraged because
     * 1) most web browsers support UTF-8 characters in URLs
     * 2) transliteration causes a loss of information
     *
     * @author Sean Murphy <sean@iamseanmurphy.com>
     *
     * @param string $str
     * @return string
     */
    public static function slug($str);

    /**
     * FIXME: Br private or not to be!
     * Private! Please do not call this function directly, just let the Tag library use it.
     * Increment count of tag by one. This function will create tag record if it does not exist.
     *
     * @param string $tagString
     * @param $tagSlug
     * @param $count
     * @return
     */
    public function incrementCount($tagString, $tagSlug, $count);

    /**
     * FIXME: Br private or not to be!
     * Private! Please do not call this function directly, let the Tag library use it.
     * Decrement count of tag by one. This function will create tag record if it does not exist.
     *
     * @param string $tagString
     * @param $tagSlug
     * @param $count
     * @return
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
    public function tagModelString();
}
