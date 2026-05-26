@extends('hajiri.layouts.app')

@php
    $weekendDays = $setting->weekendDays();
    $dayLabels = [
        0 => ['short' => 'आइत', 'name' => 'Sunday'],
        1 => ['short' => 'सोम', 'name' => 'Monday'],
        2 => ['short' => 'मंगल', 'name' => 'Tuesday'],
        3 => ['short' => 'बुध', 'name' => 'Wednesday'],
        4 => ['short' => 'बिहि', 'name' => 'Thursday'],
        5 => ['short' => 'शुक्र', 'name' => 'Friday'],
        6 => ['short' => 'शनि', 'name' => 'Saturday'],
    ];
    $manualHolidayCount = collect($holidays)->filter(fn($h) => $h->status)->count();
@endphp

@section('content')

{{-- Hero --}}
<div class="bg-[#1a5632] rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-[#e2a024] rounded-full blur-3xl opacity-20 pointer-events-none"></div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#e2a024] uppercase tracking-widest mb-1">Attendance Calendar</p>
            <h2 class="text-2xl font-extrabold">Holiday, Vacation &amp; Office Hours</h2>
            <p class="text-green-200 text-sm mt-1">Set weekends, add vacation ranges, control grace times.</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white/10 border border-white/20 rounded-xl px-5 py-3 text-center min-w-22.5">
                <span class="block text-3xl font-black leading-none">{{ count($weekendDays) }}</span>
                <span class="text-xs font-bold text-green-100 mt-1 block">Weekly off</span>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl px-5 py-3 text-center min-w-22.5">
                <span class="block text-3xl font-black leading-none">{{ $manualHolidayCount }}</span>
                <span class="text-xs font-bold text-green-100 mt-1 block">This month</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- ── Left column: settings + add range ── --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Office Rules --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Rules</p>
            <h3 class="text-lg font-extrabold text-gray-900 mb-5">Office Hours</h3>

            <form method="POST" action="{{ route('hajiri.holidays.settings') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Office starts</label>
                        <input type="time" name="office_start_time"
                               value="{{ substr($setting->office_start_time, 0, 5) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Office ends</label>
                        <input type="time" name="office_end_time"
                               value="{{ substr($setting->office_end_time, 0, 5) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Dhilo grace (min)</label>
                        <input type="number" name="late_grace_minutes"
                               value="{{ $setting->late_grace_minutes }}" min="0" max="240"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10" required>
                        <p class="text-[10px] text-gray-400 mt-1">Allowed after office start</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Xeto grace (min)</label>
                        <input type="number" name="early_grace_minutes"
                               value="{{ $setting->early_grace_minutes }}" min="0" max="240"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10" required>
                        <p class="text-[10px] text-gray-400 mt-1">Allowed before office end</p>
                    </div>
                </div>

                {{-- Weekly holiday pills --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2">Weekly holiday days</label>
                    <div class="grid grid-cols-2 gap-2" id="weekendPills">
                        @foreach($dayLabels as $day => $label)
                            @php $checked = in_array($day, $weekendDays, true); @endphp
                            <label class="weekend-pill flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl border cursor-pointer transition-all select-none
                                          {{ $checked ? 'bg-[#1a5632]/10 border-[#1a5632] text-[#1a5632]' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <input type="checkbox" name="weekend_days[]" value="{{ $day }}"
                                       class="sr-only weekend-day-input" {{ $checked ? 'checked' : '' }}>
                                <span class="text-sm font-bold">{{ $label['name'] }}</span>
                                <span class="text-[11px] font-extrabold text-[#e2a024]">{{ $label['short'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-2.5 bg-[#1a5632] hover:bg-[#0b2415] text-white text-sm font-extrabold rounded-xl transition-colors">
                    Save Attendance Rules
                </button>
            </form>
        </div>

        {{-- Add Holiday Range --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Vacation</p>
            <h3 class="text-lg font-extrabold text-gray-900 mb-5">Add Holiday Range</h3>

            <form method="POST" action="{{ route('hajiri.holidays.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Start date</label>
                        <input type="date" name="start_date" id="startDate"
                               value="{{ now()->toDateString() }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">End date</label>
                        <input type="date" name="end_date" id="endDate"
                               value="{{ now()->toDateString() }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Holiday name</label>
                    <input type="text" name="name" id="holidayName"
                           placeholder="Dashain Vacation / Local Holiday"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Short note (optional)</label>
                    <input type="text" name="alias"
                           placeholder="Short alias"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                </div>
                <label class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer">
                    <input type="checkbox" name="skip_weekends" value="1" class="w-4 h-4 accent-[#1a5632]">
                    <span class="text-xs font-semibold text-gray-600">Skip configured weekend days in this range</span>
                </label>
                <input type="hidden" name="status" value="1">
                <button type="submit"
                        class="w-full py-2.5 bg-[#1a5632] hover:bg-[#0b2415] text-white text-sm font-extrabold rounded-xl transition-colors">
                    + Add Holiday / Vacation
                </button>
            </form>
        </div>
    </div>

    {{-- ── Right column: calendar ── --}}
    <div class="xl:col-span-3">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            {{-- Calendar header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-5">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Calendar</p>
                    <h3 class="text-xl font-extrabold text-gray-900">{{ $nowData['nmonthBS'] }} {{ $nowData['yearBS'] }}</h3>
                    <p class="text-sm text-gray-500 font-medium mt-0.5">
                        {{ $nowData['nmonthAD_A'] }}/{{ $nowData['nmonthAD_B'] }} {{ $nowData['yearAD'] }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <select id="yearCal" class="changeCalendarDate text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 font-semibold focus:outline-none focus:border-[#1a5632]">
                        @foreach($npCal->bs as $bsDate)
                            <option {{ $nowData['yearBS'] == $bsDate[0] ? 'selected' : '' }} value="{{ $bsDate[0] }}">{{ $bsDate[0] }}</option>
                        @endforeach
                    </select>
                    <select id="monthCal" class="changeCalendarDate text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 font-semibold focus:outline-none focus:border-[#1a5632]">
                        @for($i = 1; $i <= 12; $i++)
                            <option {{ $nowData['monthBS'] == $i ? 'selected' : '' }} value="{{ $i }}">{{ $npCal->get_nepali_month($i) }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Day headers --}}
            <div class="grid grid-cols-7 gap-1 mb-1">
                @foreach($dayLabels as $label)
                    <div class="text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-wide py-1">
                        {{ $label['short'] }}
                    </div>
                @endforeach
            </div>

            {{-- Day cells --}}
            <div class="grid grid-cols-7 gap-1" id="calGrid">
                @for($i = 1; $i <= $nowData['firstDay'] - 1; $i++)
                    <div class="min-h-16 sm:min-h-20 rounded-xl bg-gray-50"></div>
                @endfor

                @for($i = $nowData['firstBS'], $index = 0; $i <= $nowData['lastBS']; $i++, $index++)
                    @php
                        $date    = $nowData['periodAD'][$index];
                        $dateKey = $date->format('Y-m-d');
                        $isToday  = $date->isSameDay(now());
                        $isWeekend = in_array($date->dayOfWeek, $weekendDays, true);
                        $holiday  = $holidays[$dateKey] ?? null;

                        $cell = 'bg-white border border-gray-200 hover:border-[#1a5632] hover:shadow-sm cursor-pointer transition-all';
                        if ($isToday)       $cell = 'bg-[#1a5632] border-[#1a5632] text-white cursor-pointer';
                        elseif ($holiday)   $cell = 'bg-red-600 border-red-700 text-white cursor-pointer hover:bg-red-700';
                        elseif ($isWeekend) $cell = 'bg-red-600 border-red-700 text-white cursor-pointer hover:bg-red-700';
                    @endphp
                    <button type="button"
                            class="min-h-16 sm:min-h-20 rounded-xl border p-1.5 sm:p-2 flex flex-col text-left {{ $cell }} {{ $isToday ? 'ring-2 ring-[#e2a024] ring-offset-2' : '' }}"
                            data-date="{{ $dateKey }}">
                        <span class="text-lg sm:text-2xl font-extrabold leading-none date-text {{ ($isToday || $holiday || $isWeekend) ? 'text-white' : 'text-gray-900' }}">{{ $i }}</span>
                        <span class="text-[9px] font-medium leading-none mt-0.5 {{ ($holiday || $isWeekend) ? 'text-red-100' : ($isToday ? 'text-green-200' : 'text-gray-400') }}">{{ $date->format('M d') }}</span>
                        @if($isToday)
                            <span class="mt-auto text-[9px] font-extrabold text-green-200">Today</span>
                        @elseif($holiday)
                            <span class="mt-auto text-[9px] font-extrabold text-white leading-tight truncate">{{ Str::limit($holiday->label, 8, '') }}</span>
                        @elseif($isWeekend)
                            <span class="mt-auto text-[9px] font-extrabold text-white">Off</span>
                        @endif
                    </button>
                @endfor

                @for($i = 1; $i <= 7 - $nowData['lastDay']; $i++)
                    <div class="min-h-16 sm:min-h-20 rounded-xl bg-gray-50"></div>
                @endfor
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap gap-4 mt-5 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-500">
                    <div class="w-3 h-3 rounded bg-[#1a5632]"></div> Today
                </div>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-500">
                    <div class="w-3 h-3 rounded bg-red-600 border border-red-700"></div> Weekly Off
                </div>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-500">
                    <div class="w-3 h-3 rounded bg-red-600 border border-red-700"></div> Holiday
                </div>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-500">
                    <div class="w-3 h-3 rounded bg-white border border-gray-200"></div> Working day
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Saved holidays table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mt-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Saved Dates</p>
            <h3 class="text-base font-extrabold text-gray-900 mt-0.5">Manual Holidays &amp; Vacations</h3>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Alias</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Save</th>
                    <th class="px-4 py-3 text-right text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Del</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($holidays as $holiday)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <form method="POST" action="{{ route('hajiri.holidays.update', $holiday->id) }}">
                            @csrf @method('PUT')
                            <td class="px-4 py-3 font-bold text-gray-900 whitespace-nowrap">{{ $holiday->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <input name="name" value="{{ $holiday->label }}"
                                       class="w-full px-2.5 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-[#1a5632]">
                            </td>
                            <td class="px-4 py-3">
                                <input name="alias" value="{{ $holiday->alias }}"
                                       class="w-full px-2.5 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-[#1a5632]">
                            </td>
                            <td class="px-4 py-3">
                                <select name="status"
                                        class="px-2.5 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-[#1a5632]">
                                    <option value="1" {{ $holiday->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$holiday->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button class="px-3 py-1.5 bg-[#1a5632] hover:bg-[#0b2415] text-white text-xs font-extrabold rounded-lg transition-colors">
                                    Save
                                </button>
                            </td>
                        </form>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('hajiri.holidays.destroy', $holiday->id) }}"
                                  onsubmit="return confirm('Delete this holiday?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-extrabold text-red-500 border border-red-200 hover:bg-red-50 rounded-lg transition-colors">
                                    &times;
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <p class="text-gray-400 text-sm font-medium">No manual holidays added for this month.</p>
                            <p class="text-gray-300 text-xs mt-1">Click a calendar day above to add one.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function translateNepali(s) {
        var o = 2406 - 48;
        return s.toString().split('').map(function(c) {
            var cc = c.charCodeAt(0);
            return (cc >= 48 && cc <= 57) ? String.fromCharCode(cc + o) : c;
        }).join('');
    }

    $(document).ready(function() {
        /* Translate BS numerals */
        $('.date-text').each(function() { $(this).text(translateNepali($(this).text())); });

        function refreshWeekendPills() {
            $('.weekend-pill').each(function() {
                var isChecked = $(this).find('.weekend-day-input').prop('checked');
                $(this).toggleClass('bg-[#1a5632]/10 border-[#1a5632] text-[#1a5632]', isChecked)
                       .toggleClass('border-gray-200 text-gray-600 hover:border-gray-300', !isChecked);
            });
        }

        $('.weekend-day-input').on('change', refreshWeekendPills);
        refreshWeekendPills();

        /* Calendar nav */
        $('select.changeCalendarDate').on('change', function() {
            var y = $('#yearCal').val(), m = $('#monthCal').val();
            document.location = "{{ url('/admin/hajiri/holidays/custom') }}/" + y + '/' + m;
        });

        /* Click calendar day → fill form + scroll */
        $('#calGrid button[data-date]').on('click', function() {
            var d = $(this).data('date');
            $('#startDate').val(d);
            $('#endDate').val(d);
            $('#holidayName').focus();
            $('html,body').animate({ scrollTop: $('#holidayName').offset().top - 120 }, 280);
        });

        /* Highlight range while typing */
        $('#startDate, #endDate').on('change', function() {
            var s = $('#startDate').val(), e = $('#endDate').val() || s;
            $('#calGrid button[data-date]').each(function() {
                var d = $(this).data('date');
                if (d >= s && d <= e) {
                    $(this).addClass('ring-2 ring-blue-400');
                } else {
                    $(this).removeClass('ring-2 ring-blue-400');
                }
            });
        });
    });
</script>
@endpush
