<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
                'type' => 'messages',
                'message_id' => $this->id,
                'attributes' => [
                    'body' => $this->body,
                    'sender' => new UserResource($this->sender),
                    'deleted' => $this->message->trashed(),
                    'dates' => [
                        'created_at_human' => $this->created_at->diffForHumans(),
                        'created_at' => $this->created_at
                    ]
                ]

            ]
        ];
    }
}
