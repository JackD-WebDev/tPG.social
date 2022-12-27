<?php

namespace App\Http\Controllers\Crews;

use App\Models\Crew;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Mail\SendInvitationToJoinCrew;
use App\Repositories\Contracts\CrewInterface;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\InviteInterface;
use Illuminate\Auth\Access\AuthorizationException;

class InviteController extends Controller
{
    /**
     * @var InviteInterface
     */
    protected InviteInterface $invites;
    /**
     * @var CrewInterface
     */
    protected CrewInterface $crews;
    /**
     * @var UserInterface
     */
    protected UserInterface $users;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param InviteInterface $invites
     * @param CrewInterface $crews
     * @param UserInterface $users
     * @param ResponseHelper $responseHelper
     */
    public function __construct(
        InviteInterface $invites,
        CrewInterface $crews,
        UserInterface $users,
        ResponseHelper $responseHelper)
    {
        $this->invites = $invites;
        $this->crews = $crews;
        $this->users = $users;
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
     * @param bool $user_exists
     * @param Crew $crew
     * @param string $email
     * @return void
     */
    protected function createInvite(
        bool $user_exists,
        Crew $crew,
        string $email): void
    {
        $invite = $this->invites->create([
            'crew_id' => $crew->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => $crew->id.microtime().Str::uuid()
        ]);
        Mail::to($email)
            ->send(new SendInvitationToJoinCrew($invite, $user_exists));
    }

    /**
     * @param Request $request
     * @param $crewId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function invite(Request $request, $crewId): JsonResponse
    {
        $crew = $this->crews->find($crewId);
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);
        $currentUser = $this->users->find(auth()->id());
        if(!$currentUser->isCrewOrganizer($crew)) {
            return $this->responseHelper->failureResponse(
                'UNAUTHORIZED',
                'YOU ARE NOT AN ORGANIZER FOR THIS CREW.',
                401
            );
        }
        if($crew->hasPendingInvite($request->email)) {
            return $this->responseHelper->failureResponse(
                'UNPROCESSABLE ENTITY',
                'THAT EMAIL ADDRESS ALREADY HAS A PENDING INVITE.',
                422
            );
        }
        $recipient = $this->users->findByEmail($request->email);
        if(! $recipient) {
            $this->createInvite(false, $crew, $request->email);

            return $this->responseHelper->successResponse(
                true,
                'INVITE SENT TO: ' . $request->email,
                null,
                200
            );
        }
        if($crew->hasUser($recipient)) {
            return $this->responseHelper->failureResponse(
                'UNPROCESSABLE ENTITY',
                'THAT USER IS ALREADY A MEMBER OF THIS CREW.',
                422
            );
        }
        $this->createInvite(true, $crew, $request->email);

        return $this->responseHelper->successResponse(
            true,
            'INVITE SENT TO: ' . $recipient->username,
            null,
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function resend($id): JsonResponse
    {
        $invite = $this->invites->find($id);
        $this->authorize('resend', $invite);
        $recipient = $this->users->findByEmail($invite->recipient_email);

        Mail::to($invite->recipient_email)
            ->send(new SendInvitationToJoinCrew($invite, !is_null($recipient)));

        return $this->responseHelper->successResponse(
            true,
            'INVITE RESENT.',
            null,
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
    public function respond(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);
        $token = $request->token;
        $decision = $request->decision;
        $invite = $this->invites->find($id);
        $this->authorize('respond', $invite);

        if($invite->token !== $token) {
            return $this->responseHelper->failureResponse(
                'UNAUTHORIZED',
                'INVALID TOKEN.',
                401
            );
        }
        if($decision !== 'deny') {
            $this->invites->addUserToCrew($invite->crew, auth()->id());
        }
        $invite->delete();
        return $this->responseHelper->successResponse(
            true,
            'INVITATION REFUSED.',
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
        $invite = $this->invites->find($id);
        $this->authorize('delete', $invite);
        $invite->delete();

        return $this->responseHelper->successResponse(
            true,
            'INVITATION DESTROYED.',
            null,
            200
        );
    }
}
