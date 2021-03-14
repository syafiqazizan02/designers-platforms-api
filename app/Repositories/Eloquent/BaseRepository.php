<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Arr;
use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\IBase;
use App\Repositories\Criteria\ICriteria;

abstract class BaseRepository implements IBase, ICriteria
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass(); // override getmModels
    }

    public function all() // get all data
    {
        return $this->model->get(); // dynamic pass models
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

    public function findWhereFirst($column, $value) // find first condition
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

    // bind form ICriteria
    public function withCriteria(...$criteria)
    {
        $criteria = Arr::flatten($criteria);

        // changes function criterion
        foreach($criteria as $criterion){
            $this->model = $criterion->apply($this->model); // get form protected $model;
        }

        return $this;
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
