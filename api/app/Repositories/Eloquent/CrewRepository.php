<?php
namespace App\Repositories\Eloquent;

use App\Models\Crew;
use App\Repositories\Contracts\CrewInterface;

class CrewRepository extends BaseRepository implements CrewInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Crew::class;
    }

    /**
     * @return mixed
     */
    public function fetchUserCrews(): mixed
    {
        return $this->model->where('user_id', auth()->id())->get();
    }
}
