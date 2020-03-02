<?php namespace Conner\Tagging\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Copyright (C) 2015 Robert Conner
 */
interface TaggableContract {

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param string|array $tagNames
     */
    public function addTags($tagNames);

    /**
     * Return array of the tag names related to the current model
     *
     * @return array
     */
    public function tagNames(): array;

    /**
     * Return array of the tag slugs related to the current model
     *
     * @return array
     */
    public function tagSlugs(): array;

    /**
     * Remove the tag from this model
     *
     * @param string|array|null $tagNames string or array (or null to remove all tags)
     */
    public function untag($tagNames = null);

    /**
     * Replace the tags from this model
     *
     * @param string|array $tagNames
     */
    public function retag($tagNames);

    /**
     * Return an array of all of the tags that are in use by this model
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function existingTags(): Collection;

    /**
     * Should untag on delete
     */
    public static function untagOnDelete();

    /**
     * Delete tags that are not used anymore
     */
    public static function shouldDeleteUnused(): bool;

}
