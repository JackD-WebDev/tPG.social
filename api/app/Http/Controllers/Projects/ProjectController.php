<?php

namespace App\Http\Controllers\Projects;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProjectCollection;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\ProjectInterface;
use App\Repositories\Eloquent\Criteria\{
    IsLive,
    LatestFirst,
    ForUser,
    EagerLoad
};
use Illuminate\Auth\Access\AuthorizationException;

class ProjectController extends Controller
{
    /**
     * @var ProjectInterface
     */
    protected ProjectInterface $projects;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param ProjectInterface $projects
     * @param ResponseHelper $responseHelper
     */
    public function __construct(ProjectInterface $projects, ResponseHelper $responseHelper)
    {
        $this->projects = $projects;
        $this->responseHelper = $responseHelper;
    }


    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $projects = $this->projects->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new EagerLoad(['user', 'comments']),
            new ForUser(auth()->id())
        ])->all();

        return $this->responseHelper->successResponse(
            true,
            'PROJECTS RETRIEVED SUCCESSFULLY.',
            new ProjectCollection($projects),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function findProject($id): JsonResponse
    {
        $project = $this->projects->find($id);

        return $this->responseHelper->successResponse(
            true,
            'PROJECT RETRIEVED SUCCESSFULLY.',
            new ProjectResource($project),
            200
        );
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $project = $this->projects->find($id);
        $this->authorize('update', $project);
        $this->validate($request, [
            'title' => ['required', 'unique:projects,title,' . $id],
            'category' => ['required', 'string', 'min:3', 'max:40'],
            'description' => ['required', 'string', 'min:20', 'max:200'],
            'crew' => ['required_if:assign_to_crew,true'],
            'tags' => ['required']
        ]);

        $project = $this->projects->update($id, [
            'crew_id' => $request->crew,
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$project->upload_successful ? false : $request->is_live
        ]);

        $this->projects->applyTags($id, $request->tags);

        return $this->responseHelper->successResponse(
            true,
            'PROJECT UPDATED SUCCESSFULLY.',
            new ProjectResource($project),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function support($id): JsonResponse
    {
        $this->projects->support($id);

        return $this->responseHelper->successResponse(
            true,
            'SUPPORT OPERATION SUCCESSFUL.',
            null,
            200
        );
    }

    /**
     * @param $projectId
     * @return JsonResponse
     */
    public function checkIfUserIsSupporting($projectId): JsonResponse
    {
        $isSupported = $this->projects->isSupportedByUser($projectId);
        return response()->json([
            'supported' => $isSupported
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $project = $this->projects->search($request);

        return $this->responseHelper->successResponse(
            true,
            'PROJECTS RETRIEVED SUCCESSFULLY.',
            new ProjectCollection($project),
            200
        );
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function findBySlug($slug): JsonResponse
    {
        $project = $this->projects->withCriteria([
            new IsLive()
        ])->findWhereFirst('slug', $slug);

        return $this->responseHelper->successResponse(
            true,
            'PROJECT RETRIEVED SUCCESSFULLY.',
            new ProjectResource($project),
            200
        );
    }

    /**
     * @param $crewId
     * @return JsonResponse
     */
    public function getForCrew($crewId): JsonResponse
    {
        $projects = $this->projects->withCriteria([
            new IsLive()
        ])->findWhere('crew_id', $crewId);

        return $this->responseHelper->successResponse(
            true,
            'PROJECTS RETRIEVED SUCCESSFULLY.',
            new ProjectCollection($projects),
            200
        );
    }

    /**
     * @param $userId
     * @return JsonResponse
     */
    public function getForUser($userId): JsonResponse
    {
        $projects = $this->projects->withCriteria([
            new IsLive()
        ])->findWhere('user_id', $userId);

        return $this->responseHelper->successResponse(
            true,
            'PROJECTS RETRIEVED SUCCESSFULLY.',
            new ProjectCollection($projects),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($id): JsonResponse
    {
        $project = $this->projects->find($id);
        $this->authorize('delete', $project);

        foreach (['original', 'large', 'thumbnail'] as $size) {
            $projectImage = "uploads/projects/$size/" . $project->image;
            if (Storage::disk($project->disk)->exists($projectImage)) {
                Storage::disk($project->disk)->delete($projectImage);
            }
        }

        $this->projects->delete($id);

        return $this->responseHelper->successResponse(
            true,
            'PROJECT DELETED SUCCESSFULLY.',
            null,
            200
        );
    }
}
