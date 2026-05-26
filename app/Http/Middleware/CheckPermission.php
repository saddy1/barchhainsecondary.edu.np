<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request to check if user has required permission.
     * 
     * Usage in routes:
     *   Route::post('/users', Controller@action)->middleware('permission:users.create');
     *   Route::delete('/users/{id}', Controller@action)->middleware('permission:users.delete');
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        $user = $request->user();

        if (! $user->canAccess($permissions)) {
            abort(403, 'Access denied. Required permission: ' . implode(' or ', $permissions));
        }

        return $next($request);
    }
}
