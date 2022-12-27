<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    #[ArrayShape(['data' => "array"])]
    public function toArray($request): array
    {
        return [
            'data' => [
                'type' => 'conversations',
                'conversation_id' => $this->id,
                'attributes' => [
                    'created_at_dates' => [
                        'created_at_human' => $this->created_at->diffForHumans(),
                        'created_at' => $this->created_at
                    ],
                    'is_unread' => $this->conversation->isUnreadForUser(auth()->id()),
                    'latest_message' => new MessageResource($this->latest_message),
                    'participants' => new UserCollection($this->participants)
                ],
                'links' => [
                    'self' => url('/conversations/' . $this->id),
                    'client' => url(config('app.client_url') . '/conversations/' . $this->id)
                ]
            ]
        ];
    }
}
