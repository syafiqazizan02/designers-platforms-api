<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\BaseRepository;

class DesignRepository extends BaseRepository implements IDesign
{
    public function model()
    {
        return Design::class;
    }

    public function applyTags($id, array $data) //custom function for applyTags
    {
        $design = $this->find($id);
        $design->retag($data);
    }

    public function allLive() // function to select is_live == 1
    {
        return $this->model->where('is_live', true)->get(); // override model method
    }
}
