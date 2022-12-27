<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Helpers\ResponseHelper;

class UserNotFoundException extends Exception
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
     * Render the exception as an HTTP response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return $this->responseHelper->failureResponse(
            'USER NOT FOUND',
            'UNABLE TO LOCATE USER WITH THE INFORMATION PROVIDED.',
            404
        );
    }
}
