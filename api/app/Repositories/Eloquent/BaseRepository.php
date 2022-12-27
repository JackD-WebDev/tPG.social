<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Arr;
use App\Exceptions\ModelNotDefinedException;
use App\Repositories\Contracts\BaseInterface;
use App\Repositories\Criteria\CriteriaInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

abstract class BaseRepository implements BaseInterface, CriteriaInterface
{
    /**
     * @var mixed
     */
    protected mixed $model;

    /**
     * @throws ModelNotDefinedException
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    /**
     * @return mixed
     * @throws ModelNotDefinedException
     * @throws BindingResolutionException
     */
    protected function getModelClass(): mixed
    {
        if (!method_exists($this, 'model')) {
            throw new ModelNotDefinedException();
        }

        return app()->make($this->model());
    }

    /**
     * @return mixed
     */
    public function all(): mixed
    {
        return $this->model->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    public function findWhere($column, $value): mixed
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    public function findWhereFirst($column, $value): mixed
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    /**
     * @param $perPage
     * @return mixed
     */
    public function paginate($perPage = 10): mixed
    {
        return $this->model->paginate($perPage);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->model->create($data);
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data): mixed
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id): mixed
    {
        $record = $this->find($id);
        return $record->delete();
    }

    /**
     * @param $id
     * @param array $data
     * @return void
     */
    public function applyTags($id, array $data): void
    {
        $project = $this->find($id);
        $project->retag($data);
    }

    /**
     * @param $id
     * @return void
     */
    public function support($id): void
    {
        $project = $this->model->findOrFail($id);
        if ($project->isSupportedByUser(auth()->id())) {
            $project->unSupport();
        } else {
            $project->support();
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function isSupportedByUser($id): mixed
    {
        $project = $this->model->findOrFail($id);
        return $project->isSupportedByUser(auth()->id());
    }

    /**
     * @param ...$criteria
     * @return $this
     */
    public function withCriteria(...$criteria): static
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function addComment($id, array $data): mixed
    {
        $commentable = $this->model->findOrFail($id);
        return $commentable->comments()->create($data);
    }
}
