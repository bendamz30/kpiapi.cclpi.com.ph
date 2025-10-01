<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class HandleApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        $response = $next($request);

        // If it's a 429 response and the request expects JSON, return JSON instead of HTML
        if ($response->getStatusCode() === 429 && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Too Many Requests',
                'error' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $response->headers->get('Retry-After', 60)
            ], 429);
        }

        return $response;
    }
}


