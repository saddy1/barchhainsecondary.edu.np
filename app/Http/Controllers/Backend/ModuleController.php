<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = DB::table('module_settings')
            ->orderByRaw("FIELD(`group`, 'Website', 'ERP', 'Hajiri', 'General')")
            ->orderBy('label')
            ->get();

        // Group for display
        $grouped = $modules->groupBy('group');

        return view('backend.modules.index', compact('grouped'));
    }

    public function toggle(string $key)
    {
        $module = DB::table('module_settings')->where('key', $key)->first();
        abort_if(!$module, 404);

        $newState = !$module->is_enabled;

        DB::table('module_settings')
            ->where('key', $key)
            ->update(['is_enabled' => $newState, 'updated_at' => now()]);

        ModuleService::flush();

        $label  = $module->label;
        $status = $newState ? 'enabled' : 'disabled';

        return back()->with('success', "Module '{$label}' has been {$status}.");
    }
}
