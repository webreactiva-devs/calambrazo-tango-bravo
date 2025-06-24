<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks.
     */
    public function register(): void
    {

    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof AuthenticationException) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $exception->errors(),
                ], 422);
            }
        }

        return parent::render($request, $exception);
    }
}
