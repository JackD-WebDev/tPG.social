<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
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

        $user = $request->user()->username;

        return $request->wantsJson()
            ? $this->responseHelper->successResponse(
                true,
                'LOGGED IN SUCCESSFULLY.',
                ['username' => $user],
                200
            ) : redirect()->intended(config('fortify.home'));
    }
}
