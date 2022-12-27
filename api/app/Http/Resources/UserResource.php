<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @var null
     */
    public static $wrap = null;
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
                'type' => 'users',
                'user_id' => $this->id,
                $this->mergeWhen(auth()->id() == $this->id, [
                    'has2FA' => (bool)$this->two_factor_secret,
                ]),
                'attributes' => [
                    'username' => $this->username,
                    $this->mergeWhen(auth()->check() && auth()->id() == $this->id, [
                        'email' => $this->email
                    ]),
                    'cover_image' => new UserImageResource($this->whenLoaded('coverImage')),
                    'profile_image' => new UserImageResource($this->whenLoaded('profileImage')),
                    'tagline' => $this->tagline,
                    'about' => $this->about,
                    'formatted_address' => $this->formatted_address,
                    'location' => $this->location,
                    'projects' => new ProjectCollection(
                        $this->whenLoaded('projects')
                    ),
                    'channel' => new ChannelResource(
                        $this->whenLoaded('channel')
                    ),
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
                'self' => url('/users/' . $this->id),
                'client' => url(config('app.client_url') . '/users/' . $this->id)
            ]
        ];
    }
}
