<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;

use App\Models\Card\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BulkCardController extends Controller
{
// Update these constants in both controllers
    const CARD_W   = 54.0;
    const CARD_H   = 85.6;
    const MARGIN_X = 5.5;  // Centered: (297 - 5*54 - 4*4) / 2 = 5.5mm
    const MARGIN_Y = 12.0; // Adjusted for portrait fitting
    const GAP_X    = 4.0;
    const GAP_Y    = 8.0;
    const COLS     = 5;
    const ROWS     = 2;
    public function index()
    {
        $students = Student::orderBy('member_type')->orderBy('first_name')->get();
        return view('card.cards.bulk-print', compact('students'));
    }

    /**
     * Browser print preview — opens in a new tab.
     * Builds a flat list of cards with render_url for iframe src.
     * Route: POST /bulk/preview
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
            'title'  => 'Bulk Print — ' . count($students) . ' member(s)',
            'cards'  => $cards,
            'layout' => $this->buildLayout(count($cards)),
        ]);
    }

    /**
     * Download bulk PDF.
     * Route: POST /bulk/generate
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
                if ($type === 'library' && ! $student->has_library_card) continue;
                if ($type === 'bus'     && ! $student->has_bus_pass)     continue;

                $cards[] = [
                    'student'    => $student,
                    'type'       => $type,
                    // render_url is used as iframe src — avoids srcdoc escaping
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
            'cols'      => self::COLS,
            'rows'      => self::ROWS,
            'per_page'  => $perPage,
        ];
    }
}