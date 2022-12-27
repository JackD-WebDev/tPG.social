<?php

namespace App\Http\Controllers\Channels;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\ChannelCollection;
use App\Repositories\Contracts\ChannelInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class ChannelController extends Controller
{
    protected ChannelInterface $channels;
    protected ResponseHelper $responseHelper;

    /**
     * @param ChannelInterface $channels
     * @param ResponseHelper $responseHelper
     */
    public function __construct(ChannelInterface $channels, ResponseHelper $responseHelper)
    {
        $this->channels = $channels;
        $this->responseHelper = $responseHelper;
    }


    /**
     * @return ChannelCollection|JsonResponse
     */
    public function index(): JsonResponse|ChannelCollection
    {
        $channels = $this->channels->withCriteria([
            new EagerLoad(['user'])
        ])->all();

        return $this->responseHelper->successResponse(
            true,
            'CHANNELS RETRIEVED SUCCESSFULLY.',
            new ChannelCollection($channels),
            200
        );
    }
}
