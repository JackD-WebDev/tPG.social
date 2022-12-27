<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    #[ArrayShape(['data' => "array", 'links' => "array"])]
    public function toArray($request): array
    {
        return [
            'data' => [
                'type' => 'channels',
                'channel_id' => $this->id,
                'attributes' => [
                    'name' => $this->name,
                    'creator' => new UserResource($this->whenLoaded('user')),
                    'description' => $this->description,
                    'background_image' => $this->background,
                    'logo' => $this->logo,
                    'videos' => new VideoCollection($this->whenLoaded('videos')),
                    'created_at_dates' => [
                        'created_at_human' => $this->created_at->diffForHumans(),
                        'created_at' => $this->created_at
                    ],
                    'updated_at_dates' => [
                        'updated_at_human' => $this->updated_at->diffForHumans(),
                        'updated_at' => $this->updated_at
                    ]
                ]
            ],
            'links' => [
                'self' => url('/channels/' . $this->id),
                'client' => url(config('app.client_url') . '/channels/' . $this->id)
            ]
        ];
    }
}
