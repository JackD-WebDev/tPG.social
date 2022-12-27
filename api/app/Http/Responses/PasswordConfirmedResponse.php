<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Fortify\Contracts\PasswordConfirmedResponse as PasswordConfirmedResponseContract;

class PasswordConfirmedResponse implements PasswordConfirmedResponseContract
{
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param ResponseHelper $responseHelper
     */
    public function __construct(ResponseHelper $responseHelper)
    {
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
                'PASSWORD CONFIRMED SUCCESSFULLY.',
                null,
                201
            )
            : redirect()->intended(config('fortify.home'));
    }
}
