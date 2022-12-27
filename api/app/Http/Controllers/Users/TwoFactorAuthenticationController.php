<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * @var ResponseHelper
     */
    protected ResponseHelper $responseHelper;

    /**
     * @param ResponseHelper $responseHelper
     */
    public function __construct(
        ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    /**
     * @param Request $request
     * @param EnableTwoFactorAuthentication $enable
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable): JsonResponse|RedirectResponse
    {
        $enable($request->user());

        return $request->wantsJson()
            ? $this->responseHelper->successResponse(
                true,
                'TWO-FACTOR AUTHENTICATION ENABLED SUCCESSFULLY.',
                null,
                200
            ) : back()->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * @param Request $request
     * @param DisableTwoFactorAuthentication $disable
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable): JsonResponse|RedirectResponse
    {
        $disable($request->user());

        return $request->wantsJson()
            ? $this->responseHelper->successResponse(
                true,
                'TWO-FACTOR AUTHENTICATION DISABLED SUCCESSFULLY.',
                null,
                200
            ) : back()->with('status', 'two-factor-authentication-disabled');
    }
}
