<?php

namespace Conner\Tagging\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

/**
 * Class TagRemoved
 * @package Conner\Tagging
 */
class TagRemoved
{
    use SerializesModels;

    /** @var \Illuminate\Database\Eloquent\Model * */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param  Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
