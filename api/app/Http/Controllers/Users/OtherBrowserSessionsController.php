<?php

namespace App\Http\Controllers\Users;

use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class OtherBrowserSessionsController extends Controller
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
     * @param Request $request
     * @return JsonResponse
     */
    public function getSessions(Request $request): JsonResponse
    {
        if (config('session.driver') !== 'database') {
            return $this->responseHelper->failureResponse(
                'SESSIONS UNAVAILABLE',
                'SESSION STORAGE NOT CONFIGURED FOR THIS OPERATION.',
                501
            );
        }

        $data = collect(
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(
            function ($session) use ($request) {
                $agent = tap(new Agent, fn($agent) => $agent->setUserAgent($session->user_agent));

                return [
                    'agent' => [
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                    ],
                    'ip' => $session->ip_address,
                    'isCurrentDevice' => $session->id === $request->session()->getId(),
                    'lastActive' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            }
        );

        return $this->responseHelper->successResponse(
            true,
            'BROWSER SESSIONS RETRIEVED SUCCESSFULLY.',
            $data,
            200
        );
    }

    /**
     * @param Request $request
     * @param StatefulGuard $guard
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthenticationException
     */
    public function destroy(Request $request, StatefulGuard $guard): JsonResponse
    {
        if (! Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages(
                [
                    'password' => [__('THIS PASSWORD DOES NOT MATCH OUR RECORDS.')],
                ]
            )->errorBag('logoutOtherBrowserSessions');
        }

        $guard->logoutOtherDevices($request->password);

        $this->deleteOtherSessionRecords($request);

        return $this->responseHelper->successResponse(
            true,
            'OTHER BROWSER SESSIONS LOGGED OUT SUCCESSFULLY.',
            null,
            200
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function deleteOtherSessionRecords(Request $request): JsonResponse
    {
        if (config('session.driver') !== 'database') {
            return $this->responseHelper->failureResponse(
                'SESSIONS UNAVAILABLE',
                'SESSION STORAGE NOT CONFIGURED FOR THIS OPERATION.',
                501
            );
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
        return $this->responseHelper->successResponse(
            true,
            'OTHER BROWSER SESSIONS LOGGED OUT SUCCESSFULLY.',
            null,
            200
        );
    }
}
