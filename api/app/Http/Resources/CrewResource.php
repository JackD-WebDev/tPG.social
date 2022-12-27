<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class CrewResource extends JsonResource
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
                'type' => 'crews',
                'crew_id' => $this->id,
                'attributes' => [
                    'crew_name' => $this->name,
                    'member_count' => $this->members->count(),
                    'slug' => $this->slug,
                    'organizer' => new UserResource($this->whenLoaded('organizer')),
                    'projects' => new ProjectCollection($this->whenLoaded('projects')),
                    'members' => new UserCollection($this->whenLoaded('members')),
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
                'self' => url('/crews/' . $this->id),
                'client' => url(config('app.client_url') . '/crews/' . $this->id)
            ]
        ];
    }
}
