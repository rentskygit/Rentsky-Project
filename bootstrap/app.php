<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'module.access' => \App\Http\Middleware\EnsureModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The upload is too large. Please reduce the number or size of the files.',
                ], 413);
            }

            return back()
                ->with('error', 'The upload is too large. Please reduce the number or size of the files.');
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) return null;
            if ($e instanceof \Illuminate\Validation\ValidationException) return null;
            if ($e instanceof \Illuminate\Auth\AuthenticationException) return null;
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) return null;
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) return null;

            try {
                logger()->error('Unhandled exception', [
                    'url'       => $request->fullUrl(),
                    'method'    => $request->method(),
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                    'trace'     => $e->getTraceAsString(),
                ]);
            } catch (\Throwable $logError) {
                error_log('Logging failed: ' . $logError->getMessage());
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Something went wrong. Please try again.',
                ], 500);
            }

            if ($request->isMethod('GET')) {
                return null;
            }

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again. If the problem persists, contact support.');
        });
    })->create();