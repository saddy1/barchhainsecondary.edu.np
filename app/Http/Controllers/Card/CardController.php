<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;

use App\Models\Card\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CardController extends Controller
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

    public function preview(Student $student, string $type)
    {
        return view($this->resolveView($type), compact('student'));
    }

    /**
     * Render — returns raw card HTML for use as an iframe src.
     * Fixes the srcdoc HTML-escaping problem.
     * Route: GET /cards/{student}/render/{type}
     */
    public function render(Student $student, string $type, Request $request)
    {
        $flip = $request->boolean('flip', false);
        return response(
            view($this->resolveView($type), compact('student', 'flip'))->render(),
            200,
            ['Content-Type' => 'text/html']
        );
    }

    public function download(Student $student, string $type)
    {
        $pdf = Pdf::loadView($this->resolveView($type), compact('student'))
                  ->setPaper([0, 0, 241.89, 153.07]);

        return $pdf->download(str($student->full_name)->slug('_') . "_{$type}_card.pdf");
    }

    public function printSingle(Student $student, string $type)
    {
        $this->resolveView($type);

        $cards = [[
            'student'    => $student,
            'type'       => $type,
            'render_url' => route('cards.render', [$student, $type]),
        ]];

        return view('card.cards.print-preview', [
            'title'  => $student->full_name . ' — ' . ucfirst($type) . ' Card',
            'cards'  => $cards,
            'layout' => $this->buildLayout(1),
        ]);
    }

    public function printAll(Student $student)
    {
        $cards = [[
            'student'    => $student,
            'type'       => 'id',
            'render_url' => route('cards.render', [$student, 'id']),
        ]];

        if ($student->has_library_card) {
            $cards[] = [
                'student'    => $student,
                'type'       => 'library',
                'render_url' => route('cards.render', [$student, 'library']),
            ];
        }

        if ($student->has_bus_pass) {
            $cards[] = [
                'student'    => $student,
                'type'       => 'bus',
                'render_url' => route('cards.render', [$student, 'bus']),
            ];
        }

        return view('card.cards.print-preview', [
            'title'  => $student->full_name . ' — All Cards',
            'cards'  => $cards,
            'layout' => $this->buildLayout(count($cards)),
        ]);
    }

    public function downloadAll(Student $student)
    {
        $pdf = Pdf::loadView('card.cards.all-cards', compact('student'))->setPaper('a4');
        return $pdf->download(str($student->full_name)->slug('_') . '_all_cards.pdf');
    }

    private function resolveView(string $type): string
    {
        return match ($type) {
            'id'      => 'card.cards.id-card',
            'library' => 'card.cards.library-card',
            'bus'     => 'card.cards.bus-pass',
            default   => abort(404),
        };
    }

    private function buildLayout(int $count): array
    {
        $positions = [];
        for ($i = 0; $i < $count; $i++) {
            $col = $i % self::COLS;
            $row = intdiv($i, self::COLS);
            $positions[] = [
                'x'    => self::MARGIN_X + $col * (self::CARD_W + self::GAP_X),
                'y'    => self::MARGIN_Y + $row * (self::CARD_H + self::GAP_Y),
                'page' => intdiv($i, self::COLS * self::ROWS),
            ];
        }
        return [
            'positions' => $positions,
            'card_w'    => self::CARD_W,
            'card_h'    => self::CARD_H,
            'cols'      => self::COLS,
            'rows'      => self::ROWS,
            'per_page'  => self::COLS * self::ROWS,
        ];
    }
}