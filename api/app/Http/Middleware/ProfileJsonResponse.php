<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if(!app()->bound('debugbar') || !app('debugbar')->isEnabled()) {
            return $response;
        }

        if($response instanceof JsonResponse && $request->has('_debug')) {

            $response->setData(array_merge([
                '_debugbar' => Arr::only(app('debugbar')->getData(), 'queries')
            ], $response->getData(true)));
        }

        return $response;
    }
}
