<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
                'type' => 'videos',
                'video_id' => $this->id,
                'attributes' => [
                    'channel' => new ChannelResource($this->whenLoaded('channel')),
                    'thumbnail' => $this->thumbnail,
                    'title' => $this->title,
                    'category' => $this->category,
                    'description' => $this->description,
                    'support_count' => $this->video->supports()->count(),
                    'tag_list' => [
                        'tags' => $this->tagArray,
                        'normalized' => $this->tagArrayNormalized
                    ],
                    'is_live' => $this->is_live,
                    'comment_count' => $this->video->comments()->count(),
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
                'self' => url('/videos/'.$this->id),
                'client' => url(config('app.client_url').'/videos/'.$this->id)
            ]
        ];
    }
}
