<?php
namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskInterface;

class TaskRepository extends BaseRepository implements TaskInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Task::class;
    }
}
