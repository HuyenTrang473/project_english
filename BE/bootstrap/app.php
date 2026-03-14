<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        // API dùng bearer token, không cần CSRF validation
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'sanctum/csrf-cookie',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Chỉ xử lý JSON cho request API
        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null; // Để Laravel xử lý mặc định cho web
            }

            // Validation — giữ nguyên chi tiết lỗi field
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ.',
                    'errors' => $e->errors(),
                ], 422);
            }

            // Authentication
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa xác thực. Vui lòng đăng nhập.',
                ], 401);
            }

            // 404
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài nguyên.',
                ], 404);
            }

            // Method not allowed
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phương thức không được hỗ trợ.',
                ], 405);
            }

            // Too many requests (rate limiter)
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
                ], 429);
            }

            // Mọi exception khác — ẩn detail trên production
            report($e);

            $message = app()->hasDebugModeEnabled()
                ? $e->getMessage()
                : 'Đã xảy ra lỗi hệ thống, vui lòng thử lại.';

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        });
    })->create();
