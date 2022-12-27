<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
                'type' => 'posts',
                'post_id' => $this->id,
                'attributes' => [
                    'images' => $this->images,
                    'body' => $this->body,
                    'tag_list' => [
                        'tags' => $this->tagArray,
                        'normalized' => $this->tagArrayNormalized
                    ],
                    'support_count' => $this->post->supports()->count(),
                    'comment_count' => $this->post->comments()->count(),
                    'comments' => CommentResource::collection(
                        $this->whenLoaded('comments')
                    ),
                    'posted_by' => new UserResource($this->whenLoaded('user')),
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
                'self' => url('/posts/' . $this->id),
                'client' => url(config('app.client_url') . '/posts/' . $this->id)
            ]
        ];
    }
}
