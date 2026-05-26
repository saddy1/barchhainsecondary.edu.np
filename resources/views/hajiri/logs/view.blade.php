@extends('hajiri.layouts.app')

@section('content')
<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
        <p class="text-sm font-bold uppercase tracking-widest text-white/50">Hajiri</p>
        <h1 class="mt-1 text-3xl font-extrabold">My Attendance</h1>
    </div>

    {{-- ── User Info + Month Picker ── --}}
    <div class="grid gap-4 sm:grid-cols-2">

        {{-- User card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                     style="background-color: var(--theme-primary, #1a5632);">
                    {{ strtoupper(substr(Auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-extrabold text-gray-900 leading-tight">{{ Auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Staff</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex gap-2">
                    <dt class="w-28 font-semibold text-gray-500 shrink-0">Employment Type</dt>
                    <dd class="font-semibold text-gray-900">{{ Auth()->user()->employment?->label ?? '—' }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-28 font-semibold text-gray-500 shrink-0">Designation</dt>
                    <dd class="font-semibold text-gray-900">{{ Auth()->user()->designation?->label ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Month picker card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Viewing Period</p>
                <p class="text-lg font-extrabold text-gray-900">
                    {{ $nowData['nmonthBS'] }} {{ $nowData['yearBS'] }}
                    <span class="text-sm font-semibold text-gray-400 ml-1">BS</span>
                </p>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $nowData['nmonthAD_A'] }} {{ $nowData['firstAD'] }} – {{ $nowData['nmonthAD_B'] }} {{ $nowData['lastAD'] }}, {{ $nowData['yearAD'] }}
                </p>
            </div>
            <div class="mt-4 flex gap-2">
                <select id="yearCal"
                        class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15 changeCalendarDate">
                    @foreach($npCal->bs as $bsDate)
                        <option value="{{ $bsDate[0] }}" {{ $nowData['yearBS'] == $bsDate[0] ? 'selected' : '' }}>
                            {{ $bsDate[0] }}
                        </option>
                    @endforeach
                </select>
                <select id="monthCal"
                        class="flex-1 rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15 changeCalendarDate">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $nowData['monthBS'] == $i ? 'selected' : '' }}>
                            {{ $npCal->get_nepali_month($i) }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    {{-- ── Attendance Table ── --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-extrabold text-gray-900">
                {{ ucfirst($type) }} Attendance —
                <span class="text-[#1a5632]">{{ $nowData['nmonthBS'] }} {{ $nowData['yearBS'] }}</span>
            </h2>
            @if($type === 'brief')
                <a href="{{ route('hajiri.logs.showlogs', ['type' => 'detail']) }}"
                   class="text-xs font-bold text-[#1a5632] hover:underline">View Detail →</a>
            @else
                <a href="{{ route('hajiri.logs.showlogs', ['type' => 'brief']) }}"
                   class="text-xs font-bold text-[#1a5632] hover:underline">View Brief →</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-gray-500 w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Day / बार</th>
                        <th class="px-4 py-3 text-center text-xs font-extrabold uppercase text-gray-500">Date / गते</th>
                        @if($type === 'brief')
                            <th class="px-4 py-3 text-center text-xs font-extrabold uppercase text-gray-500">Status</th>
                        @else
                            <th class="px-4 py-3 text-center text-xs font-extrabold uppercase text-gray-500">Check In</th>
                            <th class="px-4 py-3 text-center text-xs font-extrabold uppercase text-gray-500">Check Out</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @for($i = $nowData['firstBS'], $j = $nowData['firstDay'], $index = 0;
                         $i <= $nowData['lastBS'];
                         $i++, $j += ($j == 7) ? -6 : 1, $index++)
                    @php
                        $enDate    = $nowData['periodAD'][$index]->format('Y-m-d');
                        $isHoliday = isset($holidays[$enDate]);
                        $isSunday  = ($j == 7);
                        $inTime    = $attendance[$enDate]['in']  ?? '-';
                        $outTime   = $attendance[$enDate]['out'] ?? '-';
                        $present   = ($inTime !== '-' && $outTime !== '-');
                        $halfDay   = ($inTime !== '-' && $outTime === 'N/A');

                        if ($isHoliday) {
                            $rowBg = 'bg-red-50';
                        } elseif ($isSunday) {
                            $rowBg = 'bg-amber-50';
                        } else {
                            $rowBg = '';
                        }
                    @endphp
                    <tr class="{{ $rowBg }} hover:bg-gray-50/60 transition-colors">

                        {{-- Row number --}}
                        <td class="px-4 py-2.5 text-xs text-gray-400 font-semibold">{{ $index + 1 }}</td>

                        {{-- Day name --}}
                        <td class="px-4 py-2.5 font-semibold {{ $isSunday ? 'text-amber-700' : ($isHoliday ? 'text-red-700' : 'text-gray-700') }}">
                            {{ str_replace('बार', '', $npCal->NepaliBaarNP($j)) }}
                            @if($isSunday)
                                <span class="ml-1 text-[10px] font-bold bg-amber-100 text-amber-700 rounded px-1">विदा</span>
                            @endif
                        </td>

                        {{-- Date (Nepali numeral via JS) --}}
                        <td class="px-4 py-2.5 text-center font-bold date-text {{ $isHoliday ? 'text-red-700' : 'text-gray-900' }}">
                            {{ sprintf('%02d', $i) }}
                        </td>

                        @if($type === 'brief')
                        {{-- Status badge --}}
                        <td class="px-4 py-2.5 text-center">
                            @if($isHoliday)
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold bg-red-100 text-red-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    छुट्टी
                                </span>
                            @elseif($isSunday)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold bg-amber-100 text-amber-700">विदा</span>
                            @elseif($present)
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold bg-green-100 text-green-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    हाजिर
                                </span>
                            @elseif($halfDay)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold bg-blue-100 text-blue-700">आधा</span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold bg-gray-100 text-gray-500">अनुपस्थित</span>
                            @endif
                        </td>
                        @else
                        {{-- Check In --}}
                        <td class="px-4 py-2.5 text-center font-semibold {{ $inTime !== '-' && $inTime !== 'N/A' ? 'text-green-700' : 'text-gray-400' }}">
                            {{ $inTime }}
                        </td>
                        {{-- Check Out --}}
                        <td class="px-4 py-2.5 text-center font-semibold {{ $outTime !== '-' && $outTime !== 'N/A' ? 'text-blue-700' : 'text-gray-400' }}">
                            {{ $outTime }}
                        </td>
                        @endif

                        {{-- Remarks --}}
                        <td class="px-4 py-2.5 text-sm {{ $isHoliday ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                            @if($isHoliday)
                                {{ $holidays[$enDate]['label'] }}
                            @endif
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- Legend --}}
        <div class="px-5 py-3 border-t border-gray-100 flex flex-wrap gap-3 text-xs font-semibold text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-green-100 border border-green-300 inline-block"></span> हाजिर (Present)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-gray-100 border border-gray-300 inline-block"></span> अनुपस्थित (Absent)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-red-100 border border-red-300 inline-block"></span> छुट्टी (Holiday)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-amber-100 border border-amber-300 inline-block"></span> विदा (Sunday)
            </span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.date-text').forEach(function(el) {
    el.textContent = translateNumerals(el.textContent.trim(), 'devanagari');
});

document.querySelectorAll('select.changeCalendarDate').forEach(function(sel) {
    sel.addEventListener('change', function() {
        var yearCal  = document.getElementById('yearCal').value;
        var monthCal = document.getElementById('monthCal').value;
        @if($type === 'brief')
            window.location = "{{ url('admin/hajiri/logs/type/brief/custom') }}/" + yearCal + '/' + monthCal;
        @else
            window.location = "{{ url('admin/hajiri/logs/type/detail/custom') }}/" + yearCal + '/' + monthCal;
        @endif
    });
});

function translateNumerals(input, target) {
    var systems = { devanagari: 2406 },
        zero = 48, nine = 57,
        offset = (systems[target.toLowerCase()] || zero) - zero,
        output = input.toString().split(''),
        i, l = output.length, cc;
    for (i = 0; i < l; i++) {
        cc = output[i].charCodeAt(0);
        if (cc >= zero && cc <= nine) output[i] = String.fromCharCode(cc + offset);
    }
    return output.join('');
}
</script>
@endpush
