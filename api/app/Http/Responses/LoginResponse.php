<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
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
        $has2FA = (bool)$request->user()->two_factor_secret;

        return $request->wantsJson()
            ? $this->responseHelper->successResponse(
                true,
                'LOGGED IN SUCCESSFULLY.',
                ['username' => $user,
                'two_factor' => $has2FA],
                200
            )
            : redirect()->intended(config('fortify.home'));
    }
}
