<?php

namespace App\Http\Middleware\Card;

use Closure;
use Illuminate\Http\Request;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Super Admin only.');
        }

        return $next($request);
    }
}
