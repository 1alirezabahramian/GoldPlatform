<?php

use App\Http\Middleware\IdempotencyMiddleware;
use App\Http\Middleware\RequestContext;
use App\Http\Middleware\SecurityHeaders;
use App\Support\CustomerApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [__DIR__.'/../routes/api.php', __DIR__.'/../routes/kimia.php'],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(RequestContext::class);
        $middleware->append(SecurityHeaders::class);
        $middleware->alias([
            'idempotency' => IdempotencyMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*'));

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/v1/customer/*')) {
                return null;
            }

            if ($exception instanceof ValidationException) {
                return CustomerApiResponse::error(
                    $request,
                    'اطلاعات ارسال‌شده معتبر نیست.',
                    'VALIDATION_FAILED',
                    422,
                    $exception->errors(),
                );
            }

            if ($exception instanceof AuthenticationException) {
                return CustomerApiResponse::error(
                    $request,
                    'برای ادامه، ابتدا وارد حساب کاربری شوید.',
                    'UNAUTHENTICATED',
                    401,
                );
            }

            if ($exception instanceof AuthorizationException) {
                return CustomerApiResponse::error(
                    $request,
                    'اجازه انجام این درخواست را ندارید.',
                    'FORBIDDEN',
                    403,
                );
            }

            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                return CustomerApiResponse::error(
                    $request,
                    'اطلاعات درخواستی پیدا نشد.',
                    'RESOURCE_NOT_FOUND',
                    404,
                );
            }

            if ($exception instanceof TooManyRequestsHttpException) {
                return CustomerApiResponse::error(
                    $request,
                    'تعداد درخواست‌ها بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.',
                    'RATE_LIMITED',
                    429,
                );
            }

            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();

                return CustomerApiResponse::error(
                    $request,
                    match ($status) {
                        401 => 'برای ادامه، ابتدا وارد حساب کاربری شوید.',
                        403 => 'اجازه انجام این درخواست را ندارید.',
                        404 => 'اطلاعات درخواستی پیدا نشد.',
                        405 => 'این روش درخواست برای مسیر انتخاب‌شده مجاز نیست.',
                        429 => 'تعداد درخواست‌ها بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.',
                        default => 'درخواست قابل انجام نیست.',
                    },
                    match ($status) {
                        401 => 'UNAUTHENTICATED',
                        403 => 'FORBIDDEN',
                        404 => 'RESOURCE_NOT_FOUND',
                        405 => 'METHOD_NOT_ALLOWED',
                        429 => 'RATE_LIMITED',
                        default => 'REQUEST_FAILED',
                    },
                    $status,
                );
            }

            report($exception);

            return CustomerApiResponse::error(
                $request,
                'خطای داخلی رخ داد. لطفاً دوباره تلاش کنید.',
                'INTERNAL_ERROR',
                500,
            );
        });
    })->create();
