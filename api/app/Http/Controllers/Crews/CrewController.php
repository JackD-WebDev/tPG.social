<?php

namespace App\Http\Controllers\Crews;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\CrewResource;
use App\Http\Resources\CrewCollection;
use App\Repositories\Contracts\CrewInterface;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\InviteInterface;
use Illuminate\Auth\Access\AuthorizationException;
use App\Repositories\Eloquent\Criteria\LatestFirst;

class CrewController extends Controller
{
    /**
     * @var CrewInterface
     */
    protected CrewInterface $crews;
    /**
     * @var UserInterface
     */
    protected UserInterface $users;
    /**
     * @var InviteInterface
     */
    protected InviteInterface $invites;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param CrewInterface $crews
     * @param UserInterface $users
     * @param InviteInterface $invites
     * @param ResponseHelper $responseHelper
     */
    public function __construct(
        CrewInterface $crews,
        UserInterface $users,
        InviteInterface $invites,
        ResponseHelper $responseHelper)
    {
        $this->crews = $crews;
        $this->users = $users;
        $this->invites = $invites;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $crews = $this->crews->withCriteria([
            new LatestFirst()
        ])->all();

        return $this->responseHelper->successResponse(
            true,
            'CREWS RETRIEVED SUCCESSFULLY.',
            new CrewCollection($crews),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:100', 'unique:crews,name']
        ]);

        $crew = $this->crews->create([
            'organizer_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return $this->responseHelper->successResponse(
            true,
            'CREW CREATED SUCCESSFULLY.',
            new CrewResource($crew),
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
        $crew = $this->crews->find($id);
        $this->authorize('update', $crew);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:100', 'unique:crews,name,'.$id]
        ]);

        $crew = $this->crews->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return $this->responseHelper->successResponse(
            true,
            'CREW UPDATED SUCCESSFULLY.',
            new CrewResource($crew),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function findCrew($id): JsonResponse
    {
        $crew = $this->crews->find($id);

        return $this->responseHelper->successResponse(
            true,
            'CREW RETRIEVED SUCCESSFULLY.',
            new CrewResource($crew),
            200
        );
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function findBySlug($slug): JsonResponse
    {
        $crew = $this->crews->findWhereFirst('slug', $slug);

        return $this->responseHelper->successResponse(
            true,
            'CREW RETRIEVED SUCCESSFULLY.',
            new CrewResource($crew),
            200
        );
    }

    /**
     * @return JsonResponse
     */
    public function getUserCrews(): JsonResponse
    {
        $crews = $this->crews->fetchUserCrews();

        return $this->responseHelper->successResponse(
            true,
            'CREWS RETRIEVED SUCCESSFULLY.',
            new CrewResource($crews),
            200
        );
    }

    /**
     * @param $crewId
     * @param $userId
     * @return JsonResponse
     */
    public function removeFromCrew($crewId, $userId): JsonResponse
    {
        $crew = $this->crews->find($crewId);
        $user = $this->users->find($userId);
        $currentUser = $this->users->find(auth()->id());

        if($user->isCrewOrganizer($crew)) {
            return $this->responseHelper->failureResponse(
                'UNAUTHORIZED',
                'THIS USER CREATED THIS CREW.',
                401
            );
        }
        if(!$currentUser->isCrewOrganizer($crew) && auth()->id() !== $user->id) {
            return $this->responseHelper->failureResponse(
                'UNAUTHORIZED',
                'YOU ARE NOT AUTHORIZED TO REMOVE THIS USER.',
                401
            );
        }

        $this->invites->removeUserFromCrew($crew, $userId);

        return $this->responseHelper->successResponse(
            true,
            'USER REMOVED SUCCESSFULLY.',
            null,
            200
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
        $crew = $this->crews->find($id);
        $this->authorize('delete', $crew);
        $crew->delete();

        return $this->responseHelper->successResponse(
            true,
            'CREW DELETED SUCCESSFULLY.',
            null,
            200
        );
    }
}
