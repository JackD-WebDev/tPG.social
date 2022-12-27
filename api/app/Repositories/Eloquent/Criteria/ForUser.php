<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ForUser implements CriterionInterface
{
    /**
     * @var string
     */
    protected string $user_id;

    /**
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function apply($model): mixed
    {
        return $model->where('user_id', $this->user_id);
    }
}
