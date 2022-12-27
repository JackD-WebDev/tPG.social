<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use Intervention\Image\Facades\Image;
use App\Http\Resources\UserImageResource;
use App\Repositories\Contracts\UserInterface;


class UserImageController extends Controller
{
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;
    /**
     * @var UserInterface
     */
    protected UserInterface $users;

    /**
     * @param ResponseHelper $responseHelper
     * @param UserInterface $users
     */
    public function __construct(ResponseHelper $responseHelper, UserInterface $users)
    {
        $this->responseHelper = $responseHelper;
        $this->users = $users;
    }

    /**
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $user = $this->users->find(auth()->id());
        $data = request()->validate([
            'image' => '',
            'width' => '',
            'height' => '',
            'location' => ''
        ]);

        $image = $data['image']->store('user-images', 'public');

        Image::make($data['image'])
            ->fit($data['width'], $data['height'])
            ->save(storage_path('app/public/user-images/'.$data['image']->hashName()));


        $userImage = $user->images()->create([
            'path' => $image,
            'width' => $data['width'],
            'height' => $data['height'],
            'location' => $data['location']
        ]);

        return $this->responseHelper->successResponse(
            true,
            'IMAGE STORED SUCCESSFULLY.',
            new UserImageResource($userImage),
            200
        );
    }
}
