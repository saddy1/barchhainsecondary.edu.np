<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;

use App\Models\Card\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoteController extends Controller
{
    // Highest class after which students are considered graduates
    public const MAX_CLASS = 12;

    // ── Show promotion dashboard ──────────────────────────────────────────
    public function index()
    {
        $user = auth()->user();

        // All school student groups (stream + section) with counts
        $groups = Student::query()
            ->tap(fn($query) => $user->applyStudentScope($query))
            ->where('organization', 'school')
            ->where('member_type', 'student')
            ->select('stream', 'section', DB::raw('COUNT(*) as count'))
            ->groupBy('stream', 'section')
            ->orderByRaw('stream IS NULL ASC')
            ->orderBy('stream')
            ->orderBy('section')
            ->get()
            ->map(function ($g) {
                $suggestedStream  = $this->suggestPromotion($g->stream ?? '');
                $currentClass     = $this->extractClassNumber($g->stream ?? '');
                $isGradYear       = $currentClass !== null && $currentClass >= self::MAX_CLASS;

                return [
                    'stream'           => $g->stream,
                    'section'          => $g->section,
                    'count'            => $g->count,
                    'suggested_stream' => $suggestedStream,
                    'current_class'    => $currentClass,
                    'is_grad_year'     => $isGradYear,
                    'label'            => trim(($g->stream ?? '(No class)') . ' ' . ($g->section ?? '')),
                ];
            });

        return view('card.students.promote', compact('groups'));
    }

    // ── Apply promotion ───────────────────────────────────────────────────
    public function promote(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'groups'              => 'required|array|min:1',
            'groups.*.from_stream'=> 'nullable|string|max:100',
            'groups.*.from_section'=> 'nullable|string|max:50',
            'groups.*.to_stream'  => 'nullable|string|max:100',
            'groups.*.to_section' => 'nullable|string|max:50',
            'groups.*.action'     => 'required|in:promote,graduate,skip',
            'valid_till'          => 'nullable|date',
            'grad_action'         => 'required|in:mark,delete',
        ]);

        $groups     = $request->input('groups', []);
        $gradAction = $request->input('grad_action', 'mark');
        $validTill  = $request->input('valid_till');

        $promoted  = 0;
        $graduated = 0;
        $deleted   = 0;

        DB::transaction(function () use ($groups, $gradAction, $validTill, $user, &$promoted, &$graduated, &$deleted) {
            foreach ($groups as $g) {
                if ($g['action'] === 'skip') continue;

                $query = Student::query()
                    ->tap(fn($query) => $user->applyStudentScope($query))
                    ->where('organization', 'school')
                    ->where('member_type', 'student')
                    ->where('stream', $g['from_stream'] ?: null)
                    ->where('section', $g['from_section'] ?: null);

                if ($g['action'] === 'graduate') {
                    if ($gradAction === 'delete') {
                        $deleted += $query->count();
                        $query->delete();
                    } else {
                        $data = ['stream' => 'Graduated', 'section' => null];
                        if ($validTill) $data['valid_till'] = $validTill;
                        $graduated += $query->update($data);
                    }
                } else {
                    $data = [
                        'stream'  => $g['to_stream']  ?: $g['from_stream'],
                        'section' => $g['to_section'] ?: $g['from_section'] ?: null,
                    ];
                    if ($validTill) $data['valid_till'] = $validTill;
                    $promoted += $query->update($data);
                }
            }
        });

        $msg = [];
        if ($promoted)  $msg[] = "{$promoted} student(s) promoted";
        if ($graduated) $msg[] = "{$graduated} student(s) marked as Graduated";
        if ($deleted)   $msg[] = "{$deleted} student(s) removed (graduated/left)";

        return redirect()->route('students.index')
                         ->with('success', implode(', ', $msg) . '.');
    }

    // ── Auto-suggest the next class from a stream string ─────────────────
    // "Class 5"  → "Class 6"
    // "Grade 10" → "Grade 11"
    // "5"        → "6"
    // "Science"  → "Science"  (no number → unchanged)
    public static function suggestPromotion(string $stream): string
    {
        if ($stream === '' || $stream === 'Graduated') return $stream;

        // Find the last number in the string and increment it
        $result = preg_replace_callback(
            '/(\d+)(?!.*\d)/',
            fn($m) => (string)((int)$m[1] + 1),
            $stream
        );

        return $result ?? $stream;
    }

    // ── Extract the class number from a stream string (or null) ──────────
    private function extractClassNumber(string $stream): ?int
    {
        if (preg_match('/(\d+)(?!.*\d)/', $stream, $m)) {
            return (int)$m[1];
        }
        return null;
    }
}
