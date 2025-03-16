<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle ValidationException for API routes
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->error([], 'Validation error.', 422, $e->errors());
            }
        });
        // Handle AuthenticationException for API routes
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->error([], 'Authentication required.', 401);
            }
        });


        // Handle NotFoundHttpException (404 errors) for API routes
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->error([], 'Resource not found.', 404);
            }
        });

        // Handle General Exceptions (500 errors) for API routes
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                // Log the exception for debugging
                Log::error('An unexpected error occurred:', [
                    'message' => $e->getMessage(),
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);

                // Determine the response based on the environment
                $debug = config('app.debug');

                $message = $debug ? $e->getMessage() : 'An unexpected error occurred.';
                $errors = $debug ? ['exception' => $e->getMessage()] : [];

                return response()->error([], $message, 500, $errors);
            }
        });
    })->create();
