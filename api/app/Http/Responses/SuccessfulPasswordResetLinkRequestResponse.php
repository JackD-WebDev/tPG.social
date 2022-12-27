<?php

namespace App\Http\Responses;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as SuccessfulPasswordResetLinkRequestResponseContract;

class SuccessfulPasswordResetLinkRequestResponse implements SuccessfulPasswordResetLinkRequestResponseContract
{
    protected string $status;
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param string $status
     * @param ResponseHelper $responseHelper
     */
    public function __construct(string $status, ResponseHelper $responseHelper)
    {
        $this->status = $status;
        $this->responseHelper = $responseHelper;
    }

    /**
     * @param $request
     * @return JsonResponse|RedirectResponse|Response
     */
    public function toResponse($request): JsonResponse|Response|RedirectResponse
    {
        return $request->wantsJson()
            ? $this->responseHelper->successResponse(
                true,
                Str::upper(trans($this->status)),
                ['email' => $request->email],
                200
            ) : back()->with('status', trans($this->status));
    }
}
