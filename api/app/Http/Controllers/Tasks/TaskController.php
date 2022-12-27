<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use App\Enums\Tasks\TaskType;
use Illuminate\Http\JsonResponse;
use App\Enums\Tasks\TaskPriority;
use App\Enums\Tasks\TaskLocation;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\TaskResource;
use Illuminate\Validation\Rules\Enum;
use App\Http\Resources\TaskCollection;
use App\Repositories\Contracts\TaskInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Repositories\Eloquent\Criteria\{ForUser, LatestFirst};

class TaskController extends Controller
{
    /**
     * @var TaskInterface
     */
    protected TaskInterface $tasks;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param TaskInterface $tasks
     * @param ResponseHelper $responseHelper
     */
    public function __construct(TaskInterface $tasks, ResponseHelper $responseHelper)
    {
        $this->tasks = $tasks;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tasks = $this->tasks->withCriteria([
            new LatestFirst(),
            // new EagerLoad(['checklistItems']),
            new ForUser(auth()->id())
        ])->all();

        return $this->responseHelper->resourceResponse(
            message: 'task.get.success',
            data: new TaskCollection($tasks),
            code: 200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function findTask($id): JsonResponse
    {
        $task = $this->tasks->find($id);

        return $this->responseHelper->resourceResponse(
            message: 'task.find.success',
            data: new TaskResource($task),
            code: 200
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $data = request()->validate([
            'title' => 'required'
        ]);

        $task = request()->user()->tasks()->create($data);

        return $this->responseHelper->resourceResponse(
            message: 'task.create.success',
            data: new TaskResource($task),
            code: 201
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $task = $this->tasks->find($id);
        $this->authorize('update', $task);

        $this->validate($request, rules: [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'task_type' => [new Enum(TaskType::class)],
            'priority' => [new Enum(TaskPriority::class)],
            'location' => [new Enum(TaskLocation::class)],
            'notes' => 'nullable|string|max:255',
            'completed' => 'boolean',
        ]);

        $task = $this->tasks->update($id, [
            'title' => $request->title,
            'description' => $request->description,
            'task_type' => $request->task_type,
            'priority' => $request->priority,
            'location' => $request->location,
            'notes' => $request->notes,
            'completed' => $request->completed,
        ]);

        return $this->responseHelper->resourceResponse(
            message: 'task.update.success',
            data: new TaskResource($task),
            code: 200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($id): JsonResponse
    {
        $task = $this->tasks->find($id);

        $this->authorize('delete', $task);

        $this->tasks->delete($id);

        return $this->responseHelper->successResponse(
            success: true,
            message: __('task.delete.success'),
            data: null,
            code: 200
        );
    }
}
