<?php

namespace App\Exceptions;

use Throwable;
use Exception;
use Psr\Log\LogLevel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception|Throwable
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception): Response
    {
        if ($exception instanceof AuthorizationException && $request->expectsJson()) {
            return response()->json([
                "errors" => [
                    "message" => "YOU ARE NOT AUTHORIZED TO ACCESS THIS RESOURCE."
                ]
            ], 403);
        }

        if ($exception instanceof ModelNotFoundException && $request->expectsJson()) {
            return response()->json([
                "errors" => [
                    "message" => "THAT RESOURCE WAS NOT FOUND IN THE DATABASE."
                ]
            ], 404);
        }

        if ($exception instanceof ModelNotDefined && $request->expectsJson()) {
            return response()->json([
                "errors" => [
                    "message" => "MODEL NOT DEFINED."
                ]
            ], 500);
        }

        if ($exception instanceof ValidationException) {
            throw new ValidationErrorException(json_encode($exception->errors()));
        }

        return parent::render($request, $exception);
    }
}
