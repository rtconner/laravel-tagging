<?php namespace Conner\Tagging\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Copyright (C) 2015 Robert Conner
 */
interface TaggableContract {

    /**
     * Return collection of tagged rows related to the tagged model
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagged();

    /**
     * Perform the action of tagging the model with the given string
     *
     * @param string|array $tagNames
     */
    public function tag($tagNames);

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
     * Filter model to subset with the given tags
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $tagNames
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllTags(Builder $query, $tagNames): Builder;

    /**
     * Filter model to subset with the given tags
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $tagNames
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyTag(Builder $query, $tagNames): Builder;

    /**
     * Filter model to subset without the given tags
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $tagNames
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutTags(Builder $query, $tagNames): Builder;

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
