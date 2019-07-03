<?php

namespace Conner\Tagging\Events;

use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class TagRemoved
{
    use SerializesModels;

    /** @var Taggable|Model **/
    public $model;

    /*** @var string */
    public $tagSlug;

    /**
     * Create a new event instance.
     *
     * @param Taggable|Model $model
     * @param string $tagSlug
     */
    public function __construct($model, string $tagSlug)
    {
        $this->model = $model;
        $this->tagSlug = $tagSlug;
    }
}