<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiBearerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = (string) (
            config('services.api_bearer_token')
            ?: ($_ENV['API_BEARER_TOKEN'] ?? null)
            ?: getenv('API_BEARER_TOKEN')
            ?: 'demo-postman-token'
        );

        if (! hash_equals($expectedToken, (string) $request->bearerToken())) {
            return response()->json([
                'message' => 'Invalid or missing bearer token.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
