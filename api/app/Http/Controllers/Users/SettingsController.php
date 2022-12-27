<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use Illuminate\Http\RedirectResponse;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class SettingsController extends Controller
{
    /**
     * @var UserInterface
     */
    protected UserInterface $users;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param UserInterface $users
     * @param ResponseHelper $responseHelper
     */
    public function __construct(UserInterface $users, ResponseHelper $responseHelper)
    {
        $this->users = $users;
        $this->responseHelper = $responseHelper;
    }

    /**
     * @param Request $request
     * @param UpdatesUserProfileInformation $updater
     * @return JsonResponse|RedirectResponse
     */
    public function updateUser(Request                       $request,
                               UpdatesUserProfileInformation $updater): JsonResponse|RedirectResponse
    {
        $updater->update($request->user(), $request->all());
        $user = $request->user()->username;
        $email = $request->user()->email;

        return $request->wantsJson()
            ? $this->responseHelper->successResponse(
                true,
                'USER INFORMATION UPDATED SUCCESSFULLY.',
                ['username' => $user,
                    'email' => $email],
                200
            )
            : back()->with('status', 'profile-information-updated');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->users->find(auth()->id());
        $this->validate($request, [
            'tagline' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180']
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user->update(auth()->id(), [
            'tagline' => $request->tagline,
            'about' => $request->about,
            'formatted_address' => $request->formatted_address,
            'location' => $location
        ]);

        return $this->responseHelper->successResponse(
            true,
            'USER INFORMATION UPDATED SUCCESSFULLY.',
            new UserResource($user),
            200
        );
    }
}
