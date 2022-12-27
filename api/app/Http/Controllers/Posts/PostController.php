<?php

namespace App\Http\Controllers\Posts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use Intervention\Image\Facades\Image;
use App\Repositories\Contracts\PostInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Repositories\Eloquent\Criteria\{
    LatestFirst,
    ForUser,
    EagerLoad,
    WithTrashed
};


class PostController extends Controller
{
    /**
     * @var PostInterface
     */
    protected PostInterface $posts;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param PostInterface $posts
     * @param ResponseHelper $responseHelper
     */
    public function __construct(PostInterface $posts, ResponseHelper $responseHelper)
    {
        $this->posts = $posts;
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
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $data = request()->validate([
            'body' => '',
            'image' => '',
            'width' => '',
            'height' => ''
        ]);

        if (isset($data['image'])) {
            $image = $data['image']->store('uploads/post-images', 'public');

            Image::make($data['image'])
                ->fit($data['width'], $data['height'])
                ->save(storage_path('app/public/uploads/post-images/' . $data['image']->hashName()));
        }

        $post = request()->user()->posts()->create([
            'body' => $data['body'],
            'image' => $image ?? null
        ]);

        return $this->responseHelper->successResponse(
            true,
            'POST STORED SUCCESSFULLY.',
            new PostResource($post),
            201
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
        $post = $this->posts->find($id);
        $this->authorize('update', $post);

        $this->validate($request, [
            'body' => ['required'],
            'tags' => ['required'],
        ]);
        $post = $this->posts->update($id, [
            'body' => $request->body
        ]);

        $this->posts->applyTags($id, $request->tags);

        return $this->responseHelper->successResponse(
            true,
            'POST UPDATED SUCCESSFULLY.',
            new PostResource($post),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function findPost($id): JsonResponse
    {
        $post = $this->posts->find($id);

        return $this->responseHelper->successResponse(
            true,
            'POST RETRIEVED SUCCESSFULLY.',
            new PostResource($post),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function support($id): JsonResponse
    {
        $this->posts->support($id);

        return $this->responseHelper->successResponse(
            true,
            'SUPPORT OPERATION SUCCESSFUL.',
            null,
            200
        );
    }

    /**
     * @param $postId
     * @return JsonResponse
     */
    public function checkIfUserIsSupporting($postId): JsonResponse
    {
        $isSupported = $this->posts->isSupportedByUser($postId);
        return response()->json([
            'supported' => $isSupported
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($id): JsonResponse
    {
        $post = $this->posts->find($id);
        $this->authorize('delete', $post);
        $this->posts->delete($id);

        return $this->responseHelper->successResponse(
            true,
            'POST DELETED SUCCESSFULLY.',
            null,
            200
        );
    }
}
