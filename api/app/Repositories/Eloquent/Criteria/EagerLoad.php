<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Illuminate\Database\Eloquent\Relations\Relation;

class EagerLoad implements CriterionInterface
{
    /**
     * @var Relation
     */
    protected Relation $relationships;

    /**
     * @param $relationships
     */
    public function __construct($relationships)
    {
        $this->relationships = $relationships;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function apply($model): mixed
    {
        return $model->with($this->relationships);
    }
}
