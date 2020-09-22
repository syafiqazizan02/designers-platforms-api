<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\IBase;

abstract class BaseRepository implements IBase
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass(); // override getmodels
    }

    public function all()
    {
        return $this->model->all(); // dynamic pass models
    }

    protected function getModelClass() // sub class being return
    {
        // check models in repository
        if( !method_exists($this, 'model'))
        {
            throw new ModelNotDefined();
        }

        // if exist and call model
        return app()->make($this->model());
    }
}
