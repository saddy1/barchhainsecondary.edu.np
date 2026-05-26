<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleService
{
    private const CACHE_KEY = 'module_settings_map';
    private const CACHE_TTL = 300; // 5 minutes

    public static function enabled(string $key): bool
    {
        return static::all()->get($key, true);
    }

    public static function disabled(string $key): bool
    {
        return !static::enabled($key);
    }

    public static function all(): \Illuminate\Support\Collection
    {
        return Cache::remember(static::CACHE_KEY, static::CACHE_TTL, function () {
            if (!Schema::hasTable('module_settings')) {
                return collect();
            }
            return DB::table('module_settings')
                ->pluck('is_enabled', 'key')
                ->map(fn($v) => (bool) $v);
        });
    }

    public static function flush(): void
    {
        Cache::forget(static::CACHE_KEY);
    }
}
