<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class UserImageResource extends JsonResource
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
                'type' => 'user-images',
                'user_image_id' => $this->id,
                'attributes' => [
                    'path' => url($this->path),
                    'width' => $this->width,
                    'height' => $this->height,
                    'location' => $this->location,
                    'created_at_dates' => [
                        'created_at_human' => $this->created_at->diffForHumans(),
                        'created_at' => $this->created_at
                    ]
                ]
            ],
            'links' => [
                'self' => url('/users/'.$this->user_id),
                'client' => url(config('app.client_url') . '/users/' . $this->id)
            ]
        ];
    }
}
