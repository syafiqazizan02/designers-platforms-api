<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;

// diplay is_live == 1 (true)
class IsLive implements ICriterion
{
    public function apply($model)
    {
        return $model->where('is_live', true);
    }
}
