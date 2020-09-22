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

    public function all() // get all data
    {
        return $this->model->all(); // dynamic pass models
    }

    public function find($id) // get by id
    {
        $result = $this->model->findOrFail($id);
        return $result;
    }

    public function findWhere($column, $value) // find where condition
    {
        return $this->model->where($column, $value)->get();
    }

    public function findWhereFirst($column, $value) // finr first condition
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function paginate($perPage = 10) // get per page
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data) // create
    {
        $result = $this->model->create($data);
        return $result;
    }

    public function update($id, array $data) // update
    {
        $record = $this->find($id); // find($id) override method find
        $record->update($data); // then update
        return $record;
    }

    public function delete($id) // delete
    {
        $record = $this->find($id); // find($id) override method find
        return $record->delete(); // then delete
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
