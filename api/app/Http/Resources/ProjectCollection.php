<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectCollection extends ResourceCollection
{
    /**
     * @var string
     */
    public $collects = ProjectResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    #[ArrayShape(['data' => "\Illuminate\Support\Collection", 'links' => "array"])]
    public function toArray($request): array
    {
        return [
            'data' =>  $this->collection,
            'links' => [
                'self' => url('/projects'),
                'client' => url(config('app.client_url').'/projects')
            ]
        ];
    }
}
