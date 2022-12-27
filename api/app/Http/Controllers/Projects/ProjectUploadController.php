<?php

namespace App\Http\Controllers\Projects;

use Illuminate\Http\Request;
use App\Jobs\Images\UploadImage;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\ProjectInterface;

class ProjectUploadController extends Controller
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
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function upload(Request $request): JsonResponse
    {
        $this->validate($request, [
            'image' => [
                'required', 'mimes:jpeg,gif,bmp,png,apng', 'max:2048'
            ]
        ]);

        $image = $request->file('image');
        $image->getPathName();
        $filename = time()."_". preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
        $image->storeAs('uploads/original', $filename, 'stage');

        $project = $this->projects->create([
            'user_id' => auth()->id(),
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);
        $this->dispatch(new UploadImage($project));

        return $this->responseHelper->successResponse(
            true,
            'IMAGE UPLOAD INITIATED SUCCESSFULLY.',
            null,
            200
        );
    }
}
