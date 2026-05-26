<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request to check if user has required role.
     * 
     * Usage in routes:
     *   Route::post('/admin', Controller@action)->middleware('role:super-admin');
     *   Route::post('/users', Controller@action)->middleware('role:administrator|principal');
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            abort(403, 'Access denied. Required role: ' . implode(' or ', $roles));
        }

        return $next($request);
    }
}
