<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions with consistent JSON format
     */
    private function handleApiException(Throwable $e, $request)
    {
        // Validation errors
        if ($e instanceof ValidationException) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Error de validación',
                    'details' => $e->errors(),
                ],
            ], 422);
        }

        // Authentication errors
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'No autenticado',
                    'details' => null,
                ],
            ], 401);
        }

        // Not Found errors
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Recurso no encontrado',
                    'details' => null,
                ],
            ], 404);
        }

        // Server errors
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $message = config('app.debug') ? $e->getMessage() : 'Error interno del servidor';

        return response()->json([
            'ok' => false,
            'error' => [
                'code' => 'SERVER_ERROR',
                'message' => $message,
                'details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString()),
                ] : null,
            ],
        ], $statusCode);
    }
}
