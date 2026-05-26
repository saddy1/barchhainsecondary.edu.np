<?php

namespace App\Http\Controllers\Hajiri;

use App\Http\Controllers\Controller;
use App\Models\Hajiri\NepaliCalendarYear;
use Illuminate\Http\Request;

class CalendarSettingController extends Controller
{
    public function index(Request $request)
    {
        $npCal = new NepaliCalendarController();
        $selectedYear = (int) $request->query('year', now()->year + 57);
        $existing = NepaliCalendarYear::where('bs_year', $selectedYear)->first();
        $defaultYear = $npCal->getBSCal($selectedYear);
        $months = $existing?->monthDays()
            ?: ($defaultYear ? array_slice($defaultYear, 1, 12) : array_fill(0, 12, 30));

        $years = NepaliCalendarYear::with('updatedBy')
            ->orderByDesc('bs_year')
            ->get();

        return view('hajiri.calendar-settings.index', compact(
            'npCal',
            'selectedYear',
            'existing',
            'months',
            'years'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bs_year' => ['required', 'integer', 'min:2000', 'max:2199'],
            'months' => ['required', 'array', 'size:12'],
            'months.*' => ['required', 'integer', 'min:28', 'max:33'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $months = array_values(array_map('intval', $validated['months']));

        NepaliCalendarYear::updateOrCreate(
            ['bs_year' => (int) $validated['bs_year']],
            [
                'months' => $months,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('hajiri.calendar-settings.index', ['year' => $validated['bs_year']])
            ->with('message', 'Nepali calendar month array saved successfully.');
    }
}
