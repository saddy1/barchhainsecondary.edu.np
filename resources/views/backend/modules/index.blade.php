@extends('layouts.admin')

@section('title', 'Module Access Control')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="text-2xl font-bold text-[#0b2415]">Module Access Control</h1>
        <p class="text-sm text-gray-500 mt-1">Enable or disable features for this installation. Disabled modules are hidden from all menus and their routes return 403.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    @foreach($grouped as $group => $modules)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            @php
                $icon = match($group) {
                    'Website' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'ERP'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>',
                    'Hajiri'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    default   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>',
                };
            @endphp
            <svg class="w-4 h-4 text-[#1a5632]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
            <h2 class="font-semibold text-sm text-[#0b2415] uppercase tracking-wide">{{ $group }}</h2>
            <span class="ml-auto text-xs text-gray-400">{{ $modules->count() }} module{{ $modules->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($modules as $module)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50/50 transition-colors">
                <div class="flex-1 min-w-0 pr-6">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-sm text-gray-800">{{ $module->label }}</span>
                        <span class="text-[10px] font-mono text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">{{ $module->key }}</span>
                        @if(!$module->is_enabled)
                        <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full uppercase tracking-wide">Disabled</span>
                        @endif
                    </div>
                    @if($module->description)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $module->description }}</p>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.modules.toggle', $module->key) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('{{ $module->is_enabled ? 'Disable' : 'Enable' }} the {{ addslashes($module->label) }} module?')"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none
                                   {{ $module->is_enabled ? 'bg-[#1a5632]' : 'bg-gray-300' }}"
                            role="switch" aria-checked="{{ $module->is_enabled ? 'true' : 'false' }}"
                            title="{{ $module->is_enabled ? 'Click to disable' : 'Click to enable' }}">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                                     {{ $module->is_enabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <p class="text-xs text-gray-400 text-center pb-4">Changes take effect immediately. The toggle cache refreshes automatically on every change.</p>
</div>
@endsection
