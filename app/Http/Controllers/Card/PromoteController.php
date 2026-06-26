<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;

use App\Models\Card\Organization;
use App\Models\Card\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromoteController extends Controller
{
    // Highest class after which students are considered graduates
    public const MAX_CLASS = 12;

    // ── Show promotion dashboard ──────────────────────────────────────────
    public function index()
    {
        $user = auth()->user();

        // Distinct class/section groups with counts for the filter panel
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
                $suggestedStream = $this->suggestPromotion($g->stream ?? '');
                $currentClass    = $this->extractClassNumber($g->stream ?? '');
                return [
                    'stream'           => $g->stream,
                    'section'          => $g->section,
                    'count'            => $g->count,
                    'suggested_stream' => $suggestedStream,
                    'is_grad_year'     => $currentClass !== null && $currentClass >= self::MAX_CLASS,
                    'label'            => trim(($g->stream ?? '(No class)') . ($g->section ? ' / ' . $g->section : '')),
                ];
            });

        $availableStreams = $this->availableStreamsFor($user, $groups);

        return view('hr.members.promote', compact('groups', 'availableStreams'));
    }

    // ── AJAX: students in a class/section ────────────────────────────────
    public function students(\Illuminate\Http\Request $request)
    {
        $stream  = $request->input('stream');
        $section = $request->input('section');
        $q       = trim((string) $request->input('q', ''));
        $user    = auth()->user();

        $students = Student::query()
            ->tap(fn($query) => $user->applyStudentScope($query))
            ->where('organization', 'school')
            ->where('member_type', 'student')
            ->when($stream  !== null, fn ($query) => $query->where('stream',  $stream  ?: null))
            ->when($section !== null, fn ($query) => $query->where('section', $section ?: null))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('first_name', 'like', "%{$q}%")
                          ->orWhere('last_name',  'like', "%{$q}%")
                          ->orWhere('roll_number','like', "%{$q}%");
                });
            })
            ->orderBy('roll_number')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'roll_number', 'stream', 'section', 'photo'])
            ->map(fn (Student $s) => [
                'id'          => $s->id,
                'name'        => trim("{$s->first_name} {$s->middle_name} {$s->last_name}"),
                'roll_number' => $s->roll_number,
                'photo_url'   => $s->photo_url,
            ]);

        return response()->json(['students' => $students]);
    }

    // ── Apply promotion ───────────────────────────────────────────────────
    public function promote(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'groups'               => 'required|array|min:1',
            'groups.*.from_stream' => 'nullable|string|max:100',
            'groups.*.from_section'=> 'nullable|string|max:50',
            'groups.*.to_stream'   => 'nullable|string|max:100',
            'groups.*.to_section'  => 'nullable|string|max:50',
            'groups.*.action'      => 'required|in:promote,graduate,skip',
            'valid_till'           => 'nullable|date',
            'grad_action'          => 'required|in:mark,delete',
            'student_ids'          => 'nullable|array',
            'student_ids.*'        => 'integer|exists:students,id',
        ]);

        $groups     = $request->input('groups', []);
        $gradAction = $request->input('grad_action', 'mark');
        $validTill  = $request->input('valid_till');
        $studentIds = $request->input('student_ids', []);
        $availableStreams = $this->availableStreamsFor($user);

        foreach ($groups as $index => $group) {
            if (($group['action'] ?? null) !== 'promote') {
                continue;
            }

            $fromStream = trim((string) ($group['from_stream'] ?? ''));
            $toStream   = trim((string) ($group['to_stream'] ?? ''));

            if ($toStream === '') {
                throw ValidationException::withMessages([
                    "groups.{$index}.to_stream" => 'Choose a target class before promoting students.',
                ]);
            }

            if (strcasecmp($fromStream, $toStream) === 0) {
                throw ValidationException::withMessages([
                    "groups.{$index}.to_stream" => 'Target class must be different from the current class.',
                ]);
            }

            $isAvailable = $availableStreams->contains(fn ($stream) => strcasecmp($stream, $toStream) === 0);
            if (!$isAvailable) {
                throw ValidationException::withMessages([
                    "groups.{$index}.to_stream" => 'Choose a target class from the available class list.',
                ]);
            }
        }

        $promoted  = 0;
        $graduated = 0;
        $deleted   = 0;

        DB::transaction(function () use ($groups, $gradAction, $validTill, $studentIds, $user, &$promoted, &$graduated, &$deleted) {
            foreach ($groups as $g) {
                if ($g['action'] === 'skip') continue;

                $query = Student::query()
                    ->tap(fn($query) => $user->applyStudentScope($query))
                    ->where('organization', 'school')
                    ->where('member_type', 'student')
                    ->where('stream', $g['from_stream'] ?: null)
                    ->where('section', $g['from_section'] ?: null)
                    ->when(!empty($studentIds), fn ($q) => $q->whereIn('id', $studentIds));

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

        return redirect()->route('admin.hr.members.index')
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

    private function availableStreamsFor($user, $fallbackGroups = null)
    {
        $schoolOrganization = Organization::where('slug', 'school')
            ->where('is_active', true)
            ->with('activeDepartments')
            ->first();

        $streams = $schoolOrganization
            ? $schoolOrganization->activeDepartments
                ->pluck('name')
            : collect();

        if ($streams->isEmpty()) {
            $streams = $fallbackGroups
                ? $fallbackGroups->pluck('stream')
                : Student::query()
                    ->tap(fn($query) => $user->applyStudentScope($query))
                    ->where('organization', 'school')
                    ->where('member_type', 'student')
                    ->pluck('stream');
        }

        return $streams
            ->filter()
            ->reject(fn ($stream) => strcasecmp($stream, 'Graduated') === 0)
            ->unique()
            ->sort()
            ->values();
    }
}
