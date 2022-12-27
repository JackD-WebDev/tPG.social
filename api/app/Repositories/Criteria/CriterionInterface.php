<?php

namespace App\Repositories\Criteria;

interface CriterionInterface
{
    /**
     * @param $model
     * @return mixed
     */
    public function apply($model): mixed;
}
