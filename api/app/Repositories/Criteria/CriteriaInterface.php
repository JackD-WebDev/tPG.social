<?php

namespace App\Repositories\Criteria;

interface CriteriaInterface
{
    /**
     * @param ...$criteria
     * @return mixed
     */
    public function withCriteria(...$criteria): mixed;
}
