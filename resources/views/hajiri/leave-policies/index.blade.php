@extends('hajiri.layouts.app')

@section('content')

{{-- Hero --}}
<div class="bg-[#1a5632] rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-[#e2a024] rounded-full blur-3xl opacity-20 pointer-events-none"></div>
    <div class="relative z-10">
        <p class="text-[11px] font-bold text-[#e2a024] uppercase tracking-widest mb-1">HR Settings</p>
        <h2 class="text-2xl font-extrabold">Leave Policies</h2>
        <p class="text-green-200 text-sm mt-1">Define leave types, days allowed, and who they apply to</p>
    </div>
</div>

@if (session('message'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-4">{{ session('message') }}</div>
@endif
@if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

    {{-- Add / Edit Form (left) --}}
    <div class="xl:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-4">Add New Leave Policy</p>
            <form method="POST" action="{{ route('hajiri.leave-policies.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Policy Name <span class="text-gray-400 font-normal">(e.g. गृह बिदा)</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2.5 text-sm border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10"
                           placeholder="e.g. गृह बिदा, पर्व बिदा"/>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Short Code <span class="text-gray-400 font-normal">(calendar)</span></label>
                        <input type="text" name="short_code" value="{{ old('short_code') }}" required maxlength="10"
                               class="w-full px-3 py-2.5 text-sm border {{ $errors->has('short_code') ? 'border-red-400' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10 uppercase"
                               placeholder="GB"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Days Allowed</label>
                        <input type="number" name="days_allowed" value="{{ old('days_allowed') }}" required min="1" max="365"
                               class="w-full px-3 py-2.5 text-sm border {{ $errors->has('days_allowed') ? 'border-red-400' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10"
                               placeholder="10"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Period Type</label>
                    <select name="period_type" required
                            class="w-full px-3 py-2.5 text-sm border {{ $errors->has('period_type') ? 'border-red-400' : 'border-gray-200' }} rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                        <option value="annual"  {{ old('period_type') == 'annual'  ? 'selected' : '' }}>Annual (resets every Shrawan–Ashadh)</option>
                        <option value="tenure"  {{ old('period_type') == 'tenure'  ? 'selected' : '' }}>Tenure (total career allowance)</option>
                    </select>
                    @error('period_type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Applicable To</label>
                    <select name="applicable_to" required
                            class="w-full px-3 py-2.5 text-sm border {{ $errors->has('applicable_to') ? 'border-red-400' : 'border-gray-200' }} rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                        <option value="all"           {{ old('applicable_to') == 'all'           ? 'selected' : '' }}>All Staff</option>
                        <option value="teaching"      {{ old('applicable_to') == 'teaching'      ? 'selected' : '' }}>Teaching Staff only</option>
                        <option value="non_teaching"  {{ old('applicable_to') == 'non_teaching'  ? 'selected' : '' }}>Non-Teaching Staff only</option>
                    </select>
                    @error('applicable_to')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-[#1a5632] hover:bg-[#0b2415] text-white text-sm font-extrabold rounded-xl transition-colors">
                    + Add Leave Policy
                </button>
            </form>
        </div>

        {{-- Legend card --}}
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-sm text-blue-800">
            <p class="font-bold mb-2">Period Types</p>
            <p class="text-xs mb-1"><span class="font-semibold">Annual:</span> Days reset every Nepali fiscal year (Shrawan १ – Ashadh last). Example: गृह बिदा १० दिन/वर्ष.</p>
            <p class="text-xs"><span class="font-semibold">Tenure:</span> Total days across entire service period. Example: अध्ययन बिदा 3 years per career.</p>
        </div>
    </div>

    {{-- Policy List (right) --}}
    <div class="xl:col-span-3">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Active Policies ({{ $policies->count() }})</p>
            </div>

            @if($policies->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm font-extrabold text-gray-700">No policies yet</p>
                    <p class="text-xs text-gray-400 mt-1">Add your first leave policy using the form.</p>
                </div>
            @else
                {{-- Mobile: stacked cards --}}
                <div class="block lg:hidden divide-y divide-gray-50">
                    @foreach($policies as $p)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-xs font-extrabold bg-[#1a5632] text-white">{{ strtoupper($p->short_code) }}</span>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $p->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $p->applicable_label }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 text-lg font-extrabold text-[#1a5632]">{{ $p->days_allowed }}<span class="text-xs font-normal text-gray-400 ml-0.5">days</span></span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $p->period_type === 'annual' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">{{ $p->period_label }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $p->is_active ? 'Active' : 'Inactive' }}</span>
                            <form method="POST" action="{{ route('hajiri.leave-policies.toggle', $p) }}" class="ml-auto">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold text-gray-500 hover:text-[#1a5632] underline">Toggle</button>
                            </form>
                            <form method="POST" action="{{ route('hajiri.leave-policies.destroy', $p) }}" onsubmit="return confirm('Delete this policy?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[10px] font-bold text-red-400 hover:text-red-600 underline">Delete</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Desktop: table --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Policy</th>
                                <th class="px-4 py-3 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-16">Days</th>
                                <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Period</th>
                                <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Applies To</th>
                                <th class="px-4 py-3 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-20">Status</th>
                                <th class="px-4 py-3 text-right text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($policies as $p)
                            <tr class="hover:bg-gray-50 transition-colors {{ $p->is_active ? '' : 'opacity-60' }}">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[11px] font-extrabold bg-[#1a5632] text-white shrink-0">{{ strtoupper($p->short_code) }}</span>
                                        <span class="font-semibold text-gray-900">{{ $p->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-extrabold text-[#1a5632]">{{ $p->days_allowed }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $p->period_type === 'annual' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">{{ $p->period_label }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 font-medium">{{ $p->applicable_label }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('hajiri.leave-policies.toggle', $p) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-2 py-0.5 text-[10px] font-bold rounded cursor-pointer {{ $p->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }} transition-colors">
                                            {{ $p->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('hajiri.leave-policies.destroy', $p) }}" onsubmit="return confirm('Delete {{ $p->name }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-red-200 text-red-500 text-xs font-bold rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
