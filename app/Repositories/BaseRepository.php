<?php

namespace App\Repositories;

abstract class BaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->makeModel();
    }

    abstract public function getModel();

    public function makeModel()
    {
        $model = app()->make($this->getModel());
        $this->model = $model;
    }
}