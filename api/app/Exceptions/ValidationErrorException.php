<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Helpers\ResponseHelper;

class ValidationErrorException extends Exception
{
    protected ResponseHelper $responseHelper;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param ResponseHelper $responseHelper
     */
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return $this->responseHelper->failureResponse(
            'VALIDATION ERROR',
            'YOUR REQUEST IS MALFORMED OR MISSING FIELDS',
            422
        );
    }
}
