<?php namespace Conner\Tagging\Model;

trait ConnectionTrait
{

    protected function reSetConnection()
    {
        if (function_exists('config') && $connection = config('tagging.connection')) {
            $this->connection = $connection;
        }
    }
}

