<?php

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Repositories\Contracts\ConversationInterface;

class ConversationRepository extends BaseRepository implements ConversationInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Conversation::class;
    }

    /**
     * @param $conversationId
     * @param array $data
     * @return void
     */
    public function createParticipants($conversationId, array $data): void
    {
        $conversation = $this->model->find($conversationId);
        $conversation->participants()->sync($data);
    }

    /**
     * @return mixed
     */
    public function getUserConversations(): mixed
    {
        return $this->model->with(['messages', 'participants'])
            ->whereHas('participants', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
    }
}
