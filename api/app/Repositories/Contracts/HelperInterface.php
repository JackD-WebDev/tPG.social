<?php

namespace App\Repositories\Contracts;

interface HelperInterface extends BaseInterface
{
    public function successResponse($success, $message, $data, $code);
    public function failureResponse($title, $message, $code);
}
