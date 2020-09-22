<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;

// arange lastest data ones
class LatestFirst implements ICriterion
{
    public function apply($model)
    {
        return $model->latest();
    }
}
