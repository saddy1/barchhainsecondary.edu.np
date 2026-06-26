<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;

use App\Models\Card\Organization;
use App\Models\Card\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class BulkCardController extends Controller
{
    const CARD_W   = 54.0;
    const CARD_H   = 85.6;
    const MARGIN_X = 5.5;
    const MARGIN_Y = 12.0;
    const GAP_X    = 4.0;
    const GAP_Y    = 8.0;
    const COLS     = 5;
    const ROWS     = 2;

    public function index(Request $request)
    {
        $filterOptions = $this->buildFilterOptions();

        $query = Student::query();
        auth()->user()->applyStudentScope($query);

        if ($request->filled('stream')) {
            $query->where('stream', $request->stream);
        }
        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }
        if ($request->filled('type')) {
            $query->where('member_type', $request->type);
        }
        if ($request->filled('print_status')) {
            if ($request->print_status === 'printed') {
                $query->whereNotNull('card_printed_at')
                      ->whereColumn('card_printed_at', '>=', 'updated_at');
            } elseif ($request->print_status === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('card_printed_at')
                      ->orWhereColumn('card_printed_at', '<', 'updated_at');
                });
            }
        }

        // Unprinted / updated-since-print first, then most recently updated
        $students = $query
            ->orderByRaw('CASE WHEN member_type = ? THEN 0 WHEN member_type = ? THEN 1 ELSE 2 END', ['student', 'teacher'])
            ->orderByRaw('CASE WHEN card_printed_at IS NULL OR card_printed_at < updated_at THEN 0 ELSE 1 END')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('card.cards.bulk-print', compact('students', 'filterOptions'));
    }

    /**
     * Browser print preview — opens in a new tab.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'card_types'    => 'required|array|min:1',
            'card_types.*'  => 'in:id,library,bus',
        ]);

        $students  = Student::whereIn('id', $request->student_ids)->get();
        $cardTypes = $request->card_types;
        $cards     = $this->buildCardList($students, $cardTypes);

        if (empty($cards)) {
            return back()->with('error', 'No cards available for the selected members and types.');
        }

        return view('card.cards.print-preview', [
            'title'      => 'Bulk Print — ' . count($students) . ' member(s)',
            'cards'      => $cards,
            'layout'     => $this->buildLayout(count($cards)),
            'studentIds' => $request->student_ids,
        ]);
    }

    /**
     * Mark cards as printed via AJAX (called from print-preview page).
     */
    public function markPrinted(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        Student::whereIn('id', $request->student_ids)
               ->update(['card_printed_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /**
     * Download bulk PDF.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'card_types'    => 'required|array|min:1',
            'card_types.*'  => 'in:id,library,bus',
        ]);

        $students  = Student::whereIn('id', $request->student_ids)->get();
        $cardTypes = $request->card_types;

        // Mark as printed
        Student::whereIn('id', $request->student_ids)
               ->update(['card_printed_at' => now()]);

        $pdf = Pdf::loadView('card.cards.bulk-output', compact('students', 'cardTypes'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('bulk_cards_' . now()->format('Ymd_His') . '.pdf');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function buildCardList($students, array $cardTypes): array
    {
        $cards = [];

        foreach ($students as $student) {
            foreach ($cardTypes as $type) {
                if ($type !== 'id') continue; // only ID cards supported

                $cards[] = [
                    'student'    => $student,
                    'type'       => $type,
                    'render_url' => route('cards.render', [$student, $type]),
                ];
            }
        }

        return $cards;
    }

    private function buildLayout(int $count): array
    {
        $perPage   = self::COLS * self::ROWS;
        $positions = [];

        for ($i = 0; $i < $count; $i++) {
            $posOnPage = $i % $perPage;
            $col       = $posOnPage % self::COLS;
            $row       = intdiv($posOnPage, self::COLS);

            $positions[] = [
                'x'    => self::MARGIN_X + $col * (self::CARD_W + self::GAP_X),
                'y'    => self::MARGIN_Y + $row * (self::CARD_H + self::GAP_Y),
                'page' => intdiv($i, $perPage),
            ];
        }

        return [
            'positions' => $positions,
            'card_w'    => self::CARD_W,
            'card_h'    => self::CARD_H,
            'margin_x'  => self::MARGIN_X,
            'margin_y'  => self::MARGIN_Y,
            'gap_x'     => self::GAP_X,
            'gap_y'     => self::GAP_Y,
            'cols'      => self::COLS,
            'rows'      => self::ROWS,
            'per_page'  => $perPage,
        ];
    }

    private function buildFilterOptions(): array
    {
        $user = auth()->user();

        if (Schema::hasTable('organizations') && Schema::hasTable('departments') && Schema::hasTable('sections')) {
            $organizations = Organization::where('is_active', true)
                ->when(!$user->isSuperAdmin(), fn($q) => $q->where('slug', $user->organizationSlug()))
                ->with(['activeDepartments.activeSections'])
                ->orderBy('name')
                ->get();

            return $organizations->mapWithKeys(fn($org) => [
                $org->slug => [
                    'label'   => $org->name,
                    'streams' => $org->activeDepartments
                        ->when(!$user->isSuperAdmin() && $user->departmentName(), fn($d) => $d->where('name', $user->departmentName()))
                        ->mapWithKeys(fn($dept) => [
                            $dept->name => $dept->activeSections->pluck('name')->values()->all(),
                        ])->all(),
                ],
            ])->all();
        }

        return [];
    }
}
