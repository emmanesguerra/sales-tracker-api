<?php 

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException; // Import the UnauthorizedHttpException
use Symfony\Component\HttpKernel\Exception\HttpException;
use RuntimeException;
use Throwable;
use Illuminate\Support\Facades\Log;

class ExceptionHandler
{
    /**
     * Register the exception handling.
     */
    public static function register(Exceptions $exceptions): void
    {
        // Register custom exception handling
        $exceptions->render(function (ValidationException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong with your input. Please check and try again.",
                'errors' => $exception->errors(),
            ], 422);
        });

        $exceptions->render(function (NotFoundHttpException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "We couldn’t find what you were looking for. The page or resource might be missing.",
            ], 404);
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "You don’t have permission to access this resource. Please check your access rights.",
            ], 403);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "The method you tried is not supported for this action. Please use the correct method.",
            ], 405);
        });

        $exceptions->render(function (AuthenticationException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "You are not authorized to access this resource. Please check your authentication credentials.",
            ], 401);  // 401 Unauthorized
        });

        $exceptions->render(function (RequestException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong while communicating with the server. Please try again later.",
                'error' => config('app.debug') ? $exception->getMessage() : null
            ], 500);
        });

        $exceptions->render(function (QueryException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "There was an issue with our database. Please try again.",
                'error' => config('app.debug') ? $exception->getMessage() : null
            ], 500);
        });

        $exceptions->render(function (HttpException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong while processing your request. Please try again later.",
            ], $exception->getStatusCode());
        });

        $exceptions->render(function (RuntimeException $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "Something unexpected happened. Please try again later.",
            ], 500);
        });

        // Generic exception handling for uncaught exceptions
        $exceptions->render(function (Throwable $exception, $request) {
            return response()->json([
                'success' => false,
                'message' => "An unexpected error occurred. We are looking into it.",
                'error' => config('app.debug') ? $exception->getMessage() : null
            ], 500);
        });
    }
}
