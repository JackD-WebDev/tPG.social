<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class UserController extends Controller
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
    public function __construct(
        UserInterface $users,
        ResponseHelper $responseHelper)
    {
        $this->users = $users;
        $this->responseHelper = $responseHelper;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->users->withCriteria([
            new EagerLoad(['projects'])
        ])->all();

        return $this->responseHelper->successResponse(
            true,
            'USERS RETRIEVED SUCCESSFULLY.',
            new UserCollection($users),
            200
        );
    }

    /**
     * @return JsonResponse
     */
    public function getMe(): JsonResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
            return $this->responseHelper->successResponse(
                true,
                'USER INFORMATION RETRIEVED SUCCESSFULLY.',
                $user,
                200
            );
        }

        return $this->responseHelper->failureResponse(
            'UNAUTHORIZED',
            'YOU ARE NOT AUTHORIZED TO VIEW THIS RESOURCE.',
            401
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $users =  $this->users->search($request);

        return $this->responseHelper->successResponse(
            true,
            'USERS RETRIEVED SUCCESSFULLY.',
            new UserCollection($users),
            200
        );
    }

    /**
     * @param $username
     * @return JsonResponse
     */
    public function findByUsername($username): JsonResponse
    {
        $user = $this->users->findWhereFirst('username', $username);

        return $this->responseHelper->successResponse(
            true,
            'USER INFORMATION RETRIEVED SUCCESSFULLY.',
            new UserResource($user),
            200
        );
    }

    /**
     * @param $userId
     * @return JsonResponse
     */
    public function findById($userId): JsonResponse
    {
        $user = $this->users->findWhereFirst('id', $userId);

        return $this->responseHelper->successResponse(
            true,
            'USER INFORMATION RETRIEVED SUCCESSFULLY.',
            new UserResource($user),
            200
        );
    }
}
