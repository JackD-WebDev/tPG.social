<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * @param $success
     * @param $message
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    public function successResponse($success, $message, $data, $code): JsonResponse
    {
        if($data != null) {
            return response()->json([
                'success' => $success,
                'message' => strtoupper($message),
                'data' => $data
            ], $code);
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ], $code);
    }

    public function resourceResponse($message, $data, $code): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => strtoupper(__($message)),
            'data' => $data->response()->getData(true)['data'],
            'links' => $data->response()->getData(true)['links']
        ], $code);
    }

    /**
     * @param $title
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    public function failureResponse($title, $message, $code): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => [
                'title' => $title,
                'message' => $message
            ]
        ], $code);
    }
}
