<?php

namespace App\Http\Controllers\Channels;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\VideoCollection;
use App\Jobs\Videos\ConvertForStreaming;
use App\Jobs\Videos\CreateVideoThumbnail;
use App\Repositories\Contracts\VideoInterface;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Contracts\ChannelInterface;

class UploadVideoController extends Controller
{
    /**
     * @var VideoInterface
     */
    protected VideoInterface $videos;
    protected ChannelInterface $channels;
    protected ResponseHelper $responseHelper;

    /**
     * @param VideoInterface $videos
     * @param ChannelInterface $channels
     * @param ResponseHelper $responseHelper
     */
    public function __construct(VideoInterface $videos, ChannelInterface $channels, ResponseHelper $responseHelper)
    {
        $this->videos = $videos;
        $this->channels = $channels;
        $this->responseHelper = $responseHelper;
    }

    /**
     * @param $channelId
     * @return JsonResponse
     */
    public function index($channelId): JsonResponse
    {
        $videos = $this->videos->withCriteria([
            new IsLive()
        ])->findWhere('channel_id', $channelId);

        return $this->responseHelper->successResponse(
            true,
            'VIDEOS RETRIEVED SUCCESSFULLY.',
            new VideoCollection($videos),
            200
        );
    }

    /**
     * @param $channelId
     * @return mixed
     */
    public function store($channelId): mixed
    {
        $channel = $this->channels->find($channelId);

        $video = $channel->videos()->create([
            'title' => request()->title,
            'path' => request()->video->store("channels/$channelId")
        ]);

        $this->dispatch(new CreateVideoThumbnail($video));

        $this->dispatch(new ConvertForStreaming($video));

        return $video;
    }
}
