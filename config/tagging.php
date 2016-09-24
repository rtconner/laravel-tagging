<?php

return [

    /*
     * Data type for primary keys of your models.
     * Possible values : 'string', 'integer'
     *
     * Default 'integer'
     * */

    'primary_keys_type' => 'integer',

    /*
     * Value of are passed through this before save of tags
     *
     * Default '\Conner\Tagging\Util::slug'
     * */
    'normalizer' => '\Conner\Tagging\Util::slug',

    /*
     * Display value of tags are passed through (for front end display)
     * Also known as transformer or formatter.
     *
     * Default '\Illuminate\Support\Str::title'
     * */
    'displayer' => '\Illuminate\Support\Str::title',

    /*
     * Database connection for Conner\Taggable\Tag model to use
     *
     * Default null
     * */
    'connection' => null,

    /*
     * When deleting a model, remove all the tags first.
     *
     * Default true
     * */
    'untag_on_delete' => true,

    /*
     * Auto-delete unused tags from the 'tags' database table (when they are used zero times)
     *
     * Default true
     * */
    'delete_unused_tags' => true,

    /*
     * Model to use to store the tags in the database
     *
     * Default '\Conner\Tagging\Model\Tag'
     * */
    'tag_model' => '\Conner\Tagging\Model\Tag',

    /*
     * Slug delimiter
     *
     * Default '-'
     * */
    'delimiter' => '-',

    /*
     * Model to use for the relation between tags and tagged records
     *
     * Default '\Conner\Tagging\Model\Tagged'
     * */
    'tagged_model' => '\Conner\Tagging\Model\Tagged',
];
