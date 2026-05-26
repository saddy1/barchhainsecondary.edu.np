@extends('hajiri.layouts.app')

@section('content')
@php
    $monthNames = [
        1 => 'Baisakh / वैशाख',
        2 => 'Jestha / जेठ',
        3 => 'Ashadh / असार',
        4 => 'Shrawan / साउन',
        5 => 'Bhadra / भदौ',
        6 => 'Ashwin / असोज',
        7 => 'Kartik / कार्तिक',
        8 => 'Mangsir / मंसिर',
        9 => 'Poush / पुष',
        10 => 'Magh / माघ',
        11 => 'Falgun / फागुन',
        12 => 'Chaitra / चैत',
    ];
@endphp

<div class="space-y-6">
    <div class="bg-[#1a5632] rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-48 h-48 bg-[#e2a024] rounded-full blur-3xl opacity-20 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold text-[#e2a024] uppercase tracking-widest mb-1">Super Admin</p>
                <h2 class="text-2xl font-extrabold leading-tight">Nepali Calendar Month Array</h2>
                <p class="text-green-200 text-sm mt-1">
                    Current converter supports BS {{ $npCal->minBsYear() }} to {{ $npCal->maxBsYear() }}. Add future BS years here before reports need them.
                </p>
            </div>
            <form method="GET" action="{{ route('hajiri.calendar-settings.index') }}" class="flex gap-2">
                <input type="number" name="year" value="{{ $selectedYear }}" min="2000" max="2199"
                       class="w-28 rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-sm font-bold text-white placeholder:text-white/70 focus:outline-none focus:ring-2 focus:ring-[#e2a024]">
                <button class="rounded-xl bg-[#e2a024] px-4 py-2 text-sm font-extrabold text-white hover:bg-[#f4b63e]">
                    Load
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Month Lengths</p>
                    <h3 class="text-lg font-extrabold text-gray-900 mt-1">BS {{ $selectedYear }}</h3>
                </div>
                @if($existing)
                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 border border-green-100">
                        Saved override
                    </span>
                @else
                    <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-bold text-gray-500 border border-gray-100">
                        Built-in/default
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('hajiri.calendar-settings.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="bs_year" value="{{ $selectedYear }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($monthNames as $month => $label)
                        <label class="block rounded-xl border border-gray-100 bg-gray-50 p-3">
                            <span class="block text-xs font-bold text-gray-500 mb-1.5">{{ $month }}. {{ $label }}</span>
                            <input type="number" name="months[]" min="28" max="33" required
                                   value="{{ old('months.' . ($month - 1), $months[$month - 1] ?? 30) }}"
                                   class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-extrabold text-gray-900 focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                        </label>
                    @endforeach
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes', $existing->notes ?? '') }}"
                           placeholder="Source or reason for update"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                </div>

                <div class="rounded-xl bg-amber-50 border border-amber-100 px-4 py-3 text-xs text-amber-800">
                    Save years continuously. For example, add 2091 before adding 2092, because BS/AD conversion counts days from BS 2000 forward.
                </div>

                <button class="w-full sm:w-auto rounded-xl bg-[#1a5632] px-5 py-2.5 text-sm font-extrabold text-white hover:bg-[#0b2415]">
                    Save Calendar Array
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Saved Overrides</p>
                <h3 class="text-base font-extrabold text-gray-900 mt-1">Database Years</h3>
            </div>

            @if($years->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-gray-400">
                    No custom calendar years saved yet.
                </div>
            @else
                <div class="divide-y divide-gray-50 max-h-[520px] overflow-y-auto">
                    @foreach($years as $year)
                        <a href="{{ route('hajiri.calendar-settings.index', ['year' => $year->bs_year]) }}"
                           class="block px-5 py-3 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-extrabold text-gray-900">BS {{ $year->bs_year }}</p>
                                <span class="text-[10px] font-bold text-gray-400">{{ $year->updated_at?->format('d M Y') }}</span>
                            </div>
                            <p class="mt-1 font-mono text-[11px] text-gray-500 truncate">{{ implode(', ', $year->monthDays()) }}</p>
                            @if($year->notes)
                                <p class="mt-1 text-xs text-gray-400 truncate">{{ $year->notes }}</p>
                            @endif
                            @if($year->updatedBy)
                                <p class="mt-1 text-[10px] text-gray-300">Updated by {{ $year->updatedBy->name }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
