<?php

namespace App\Http\Middleware\Card;

use Closure;
use Illuminate\Http\Request;

class ActiveAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Contact the super admin.']);
        }

        return $next($request);
    }
}
