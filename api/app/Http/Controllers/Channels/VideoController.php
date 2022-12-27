<?php

namespace App\Http\Controllers\Channels;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\VideoResource;
use App\Repositories\Contracts\VideoInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class VideoController extends Controller
{
    /**
     * @var VideoInterface
     */
    protected VideoInterface $videos;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param VideoInterface $videos
     * @param ResponseHelper $responseHelper
     */
    public function __construct(VideoInterface $videos, ResponseHelper $responseHelper)
    {
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
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $video = $this->videos->find($id);

        $this->videos->incrementViews($id);

        return $this->responseHelper->successResponse(
            true,
            'VIDEO RETRIEVED SUCCESSFULLY.',
            new VideoResource($video),
            200
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
        $video = $this->videos->find($id);
        $this->authorize('update', $video);
        $this->validate($request, [
            'title' => ['required', 'unique:videos,title,' . $id],
            'category' => ['required', 'string', 'min:3', 'max:40'],
            'description' => ['required', 'string', 'min:20', 'max:200'],
            'tags' => ['required']
        ]);

        $video = $this->videos->update($id, [
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'is_live' => !$video->upload_successful ? false : $request->is_live
        ]);

        $this->videos->applyTags($id, $request->tags);

        return $this->responseHelper->successResponse(
            true,
            'VIDEO UPDATED SUCCESSFULLY.',
            new VideoResource($video),
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Video $video
     * @return void
     */
    public function destroy(Video $video): void
    {
        //
    }
}
