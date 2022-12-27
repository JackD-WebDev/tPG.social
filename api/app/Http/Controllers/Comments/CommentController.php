<?php

namespace App\Http\Controllers\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Traits\Supportable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\PostInterface;
use App\Repositories\Contracts\VideoInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Contracts\ProjectInterface;
use Illuminate\Auth\Access\AuthorizationException;

class CommentController extends Controller
{
    use Supportable;

    /**
     * @var PostInterface
     */
    protected PostInterface $posts;
    /**
     * @var CommentInterface
     */
    protected CommentInterface $comments;
    /**
     * @var ProjectInterface
     */
    protected ProjectInterface $projects;
    /**
     * @var VideoInterface
     */
    protected VideoInterface $videos;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param PostInterface $posts
     * @param CommentInterface $comments
     * @param ProjectInterface $projects
     * @param VideoInterface $videos
     * @param ResponseHelper $responseHelper
     */
    public function __construct(
        PostInterface $posts,
        CommentInterface $comments,
        ProjectInterface $projects,
        VideoInterface $videos,
        ResponseHelper $responseHelper)
    {
        $this->posts = $posts;
        $this->comments = $comments;
        $this->projects = $projects;
        $this->videos = $videos;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(): void
    {
        //
    }

    /**
     * @param Request $request
     * @param $projectId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeProjectComment(Request $request, $projectId): JsonResponse
    {
        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->projects->addComment($projectId, [
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return $this->responseHelper->successResponse(
            true,
            'COMMENT STORED SUCCESSFULLY.',
            new CommentResource($comment),
            200
        );
    }

    /**
     * @param Request $request
     * @param $videoId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeVideoComment(Request $request, $videoId): JsonResponse
    {
        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->videos->addComment($videoId, [
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return $this->responseHelper->successResponse(
            true,
            'COMMENT STORED SUCCESSFULLY.',
            new CommentResource($comment),
            200
        );
    }

    /**
     * @param Request $request
     * @param $postId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storePostComment(Request $request, $postId): JsonResponse
    {
        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->posts->addComment($postId, [
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return $this->responseHelper->successResponse(
            true,
            'COMMENT STORED SUCCESSFULLY.',
            new CommentResource($comment),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $comment = $this->comments->find($id);
        $this->authorize('update', $comment);

        $this->validate($request, [
            'body' => ['required']
        ]);
        $comment = $this->comments->update($id, [
            'body' => $request->body
        ]);

        return $this->responseHelper->successResponse(
            true,
            'COMMENT UPDATED SUCCESSFULLY.',
            new CommentResource($comment),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function support($id): JsonResponse
    {
        $this->comments->support($id);

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
        $isSupported = $this->comments->isSupportedByUser($projectId);
        return response()->json([
            'supported' => $isSupported
        ]);
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
        $comment = $this->comments->find($id);
        $this->authorize('delete', $comment);
        $this->comments->delete($id);

        return $this->responseHelper->successResponse(
            true,
            'COMMENT DELETED SUCCESSFULLY.',
            null,
            200
        );
    }

}
