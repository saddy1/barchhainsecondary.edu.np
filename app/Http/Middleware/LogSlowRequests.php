<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSlowRequests
{
    /**
     * Handle an incoming request and log slow page loads
     * Helps identify performance bottlenecks
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000; // Convert to milliseconds

        // Log requests that take longer than 500ms
        if ($duration > 500) {
            Log::warning('Slow request detected', [
                'path' => $request->path(),
                'method' => $request->method(),
                'duration_ms' => round($duration, 2),
                'user_id' => auth()->id(),
            ]);
        }

        // Add performance header for debugging
        $response->header('X-Response-Time', round($duration, 2) . 'ms');

        return $response;
    }
}
