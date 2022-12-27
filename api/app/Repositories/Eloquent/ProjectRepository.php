<?php
namespace App\Repositories\Eloquent;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Repositories\Contracts\ProjectInterface;

class ProjectRepository extends BaseRepository implements ProjectInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Project::class;
    }

    /**
     * @param Request $request
     * @return mixed|void
     */
    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();
        $query->where('is_live', true);

        if($request->has_comments) {
            $query->has('comments');
        }

        if($request->has_crew) {
            $query->has('crew');
        }

        if($request->q) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'support', '%'.$request->q.'%')
                    ->orWhere('description', 'support', '%'.$request->q.'%');
            });

            if($request->orderBy=='supports') {
                $query->withCount('supports')
                    ->orderByDesc('supports_count');
            } else {
                $query->latest();
            }

            return $query->get();
        }
    }
}
