<?php namespace Conner\Tagging\Events;

use Conner\Tagging\Taggable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class TagAdded
{
    use SerializesModels;

    /** @var \Illuminate\Database\Eloquent\Model **/
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