<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
                'type' => 'projects',
                'project_id' => $this->id,
                'attributes' => [
                    'created_by' => new UserResource($this->whenLoaded('user')),
                    'images' => $this->images,
                    'title' => $this->title,
                    'category' => $this->category,
                    'description' => $this->description,
                    'support_count' => $this->project->supports()->count(),
                    'tag_list' => [
                        'tags' => $this->tagArray,
                        'normalized' => $this->tagArrayNormalized
                    ],
                    'slug' => $this->slug,
                    'crew' => $this->crew ? [
                        'id' => $this->crew->id,
                        'name' => $this->crew->name,
                        'slug' => $this->crew->slug
                    ] : null,
                    'is_live' => $this->is_live,
                    'comment_count' => $this->project->comments()->count(),
                    'comments' => CommentResource::collection($this->whenLoaded('comments')),
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
                'self' => url('/projects/'.$this->id),
                'client' => url(config('app.client_url').'/projects/'.$this->id)
            ]
        ];
    }
}
