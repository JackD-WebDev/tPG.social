<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class LatestFirst implements CriterionInterface
{
    /**
     * @param $model
     * @return mixed
     */
    public function apply($model): mixed
    {
        return $model->latest();
    }
}
