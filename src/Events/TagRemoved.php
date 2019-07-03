<?php

namespace Conner\Tagging\Events;

use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class TagRemoved
{
    use SerializesModels;

    /** @var \Conner\Tagging\Taggable **/
    public $model;

    /**
     * Create a new event instance.
     *
     * @param Taggable|Model $model
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }
}