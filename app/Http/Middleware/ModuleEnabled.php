<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;

class ModuleEnabled
{
    public function handle(Request $request, Closure $next, string $moduleKey): mixed
    {
        if (!ModuleService::enabled($moduleKey)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This module is not enabled.'], 403);
            }
            abort(403, "The '{$moduleKey}' module is not enabled for this installation.");
        }

        return $next($request);
    }
}
