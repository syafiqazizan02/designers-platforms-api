<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;

// use for soft-delete
class WithTrashed implements ICriterion
{
    public function apply($model)
    {
        return $model->withTrashed();
    }
}
