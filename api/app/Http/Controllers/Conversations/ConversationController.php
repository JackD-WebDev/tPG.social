<?php

namespace App\Http\Controllers\Conversations;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ConversationResource;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\MessageInterface;
use Illuminate\Auth\Access\AuthorizationException;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use App\Repositories\Contracts\ConversationInterface;

class ConversationController extends Controller
{
    /**
     * @var UserInterface
     */
    protected UserInterface $users;
    /**
     * @var ConversationInterface
     */
    protected ConversationInterface $conversations;
    /**
     * @var MessageInterface
     */
    protected MessageInterface $messages;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param UserInterface $users
     * @param ConversationInterface $conversations
     * @param MessageInterface $messages
     * @param ResponseHelper $responseHelper
     */
    public function __construct(
        UserInterface $users,
        ConversationInterface $conversations,
        MessageInterface $messages, ResponseHelper $responseHelper)
    {
        $this->users = $users;
        $this->conversations = $conversations;
        $this->messages = $messages;
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
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $this->validate($request, [
            'recipient' => ['required'],
            'body' => ['required']
        ]);

        $recipient = $request->recipient;
        $user = $this->users->findWhereFirst('id', auth()->id());
        $body = $request->body;

        $conversation = $user->getConversationWithUser($recipient);

        if(! $conversation) {
            $conversation = $this->conversations->create([]);
            $this->conversations->createParticipants($conversation->id, [$user->id, $recipient]);
        }

        $message = $this->messages->create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'body' => $body,
            'last_read' => null
        ]);

        return $this->responseHelper->successResponse(
            true,
            'MESSAGE STORED SUCCESSFULLY.',
            new MessageResource($message),
            200
        );
    }

    /**
     * @return JsonResponse
     */
    public function getUserConversations(): JsonResponse
    {
        $conversations = $this->conversations->getUserConversations();

        return $this->responseHelper->successResponse(
            true,
            'CONVERSATIONS RETRIEVED SUCCESSFULLY.',
            ConversationResource::collection($conversations),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getConversationMessages($id): JsonResponse
    {
        $messages = $this->messages->withCriteria([
            new WithTrashed()
        ])->findWhere('conversation_id', $id);

        return $this->responseHelper->successResponse(
            true,
            'MESSAGES RETRIEVED SUCCESSFULLY.',
            MessageResource::collection($messages),
            200
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function markAsRead($id): JsonResponse
    {
        $conversation = $this->conversations->find($id);
        $conversation->markAsReadForUser(auth()->id());

        return $this->responseHelper->successResponse(
            true,
            'MESSAGE MARKED AS READ.',
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
        $message = $this->messages->find($id);
        $this->authorize('delete', $message);
        $message->delete();

        return $this->responseHelper->successResponse(
            true,
            'MESSAGE DELETED SUCCESSFULLY.',
            null,
            200
        );
    }
}
