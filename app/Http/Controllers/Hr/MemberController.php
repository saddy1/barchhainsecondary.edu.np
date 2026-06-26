<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Card\Organization as CardOrganization;
use App\Models\Card\Student;
use App\Models\User;
use App\Models\Hajiri\Department as HajiriDepartment;
use App\Models\Hajiri\Designation;
use App\Models\Hajiri\EmploymentType;
use App\Models\Hajiri\WorkAssigned;
use App\Services\MemberAccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);
        if (! in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 20;
        }

        $query = Student::query()->with('user.roles');

        $query->when($request->filled('type'), fn ($q) => $q->where('member_type', $request->type))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("TRIM(CONCAT_WS(' ', first_name, middle_name, last_name)) LIKE ?", ["%{$search}%"])
                        ->orWhere('roll_number', 'like', "%{$search}%")
                        ->orWhere('registration_no', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('stream'), fn ($q) => $q->where('stream', $request->stream))
            ->when($request->filled('section'), fn ($q) => $q->where('section', $request->section))
            ->when($request->filled('permanent_district'), fn ($q) => $q->where('permanent_district', $request->permanent_district))
            ->when($request->filled('permanent_municipality'), fn ($q) => $q->where('permanent_municipality', $request->permanent_municipality));

        $members = $query->latest()->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('hr.members._table', compact('members'))->render(),
            ]);
        }

        $counts = [
            'all' => Student::count(),
            'student' => Student::where('member_type', 'student')->count(),
            'teacher' => Student::where('member_type', 'teacher')->count(),
            'staff' => Student::where('member_type', 'staff')->count(),
        ];

        // Teacher/staff users created via Hajiri that have no HR (students) record yet
        $typeFilter = $request->input('type');
        $showOrphans = ! $typeFilter || $typeFilter === 'teacher' || $typeFilter === 'staff';
        $orphanUsers = $showOrphans
            ? User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['teacher', 'staff']))
                ->whereDoesntHave('student')
                ->when($typeFilter, fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $typeFilter)))
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->input('search');
                    $q->where(function ($inner) use ($search) {
                        $inner->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->get()
            : collect();

        // Filter options for the UI
        $streams = Student::query()->whereNotNull('stream')->where('stream', '!=', '')->distinct()->orderBy('stream')->pluck('stream');
        $sections = Student::query()->whereNotNull('section')->where('section', '!=', '')->distinct()->orderBy('section')->pluck('section');
        $districts = Student::query()->whereNotNull('permanent_district')->where('permanent_district', '!=', '')->distinct()->orderBy('permanent_district')->pluck('permanent_district');
        $municipalities = Student::query()->whereNotNull('permanent_municipality')->where('permanent_municipality', '!=', '')->distinct()->orderBy('permanent_municipality')->pluck('permanent_municipality');

        return view('hr.members.index', compact('members', 'counts', 'orphanUsers', 'streams', 'sections', 'districts', 'municipalities'));
    }

    public function create(Request $request)
    {
        $prefillUser = null;
        if ($request->filled('prefill_user')) {
            $prefillUser = User::with('roles')
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['teacher', 'staff']))
                ->find((int) $request->prefill_user);
        }

        return view('hr.members.form', [
            'member'      => null,
            'prefillUser' => $prefillUser,
            'formOptions' => $this->buildFormOptions(),
            'hajiriOptions' => $this->hajiriOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $password = $request->input('password');
        $loginUserId = $request->input('login_user_id');
        $deviceId = $request->input('device_id');
        $hajiriData = $this->hajiriData($request);
        unset($data['login_user_id'], $data['password'], $data['password_confirmation'], $data['device_id'], $data['designation_id'], $data['employment_type_id'], $data['work_assigned_id'], $data['hajiri_department_id']);

        if (($data['member_type'] ?? null) === 'student') {
            $data['program'] = $data['stream'] ?? null;
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storePhoto($request, $data['roll_number']);
        } elseif ($request->filled('photo_capture')) {
            $data['photo'] = $this->storeBase64Photo($request->input('photo_capture'), $data['roll_number']);
        }

        DB::transaction(function () use ($data, $password, $loginUserId, $deviceId, $hajiriData) {
            $member = Student::create($data);
            $user = app(MemberAccountService::class)->sync($member, $password, $loginUserId);

            if (in_array($member->member_type, ['teacher', 'staff'], true)) {
                $user->forceFill(array_merge($hajiriData, [
                    'device_id' => $deviceId ?: $user->device_id,
                ]))->save();
            }
        });

        return redirect()->route('admin.hr.members.index')->with('success', 'Member created and synced across ERP modules.');
    }

    public function edit(Student $member)
    {
        return view('hr.members.form', [
            'member'      => $member->load('user'),
            'prefillUser' => null,
            'formOptions' => $this->buildFormOptions(),
            'hajiriOptions' => $this->hajiriOptions(),
        ]);
    }

    public function show(Student $member)
    {
        return view('hr.members.show', [
            'member' => $member->load('user.roles'),
        ]);
    }

    public function update(Request $request, Student $member)
    {
        $data = $this->validated($request, $member);
        $password = $request->input('password');
        $loginUserId = $request->input('login_user_id');
        $deviceId = $request->input('device_id');
        $hajiriData = $this->hajiriData($request);
        unset($data['login_user_id'], $data['password'], $data['password_confirmation'], $data['device_id'], $data['designation_id'], $data['employment_type_id'], $data['work_assigned_id'], $data['hajiri_department_id']);

        if (($data['member_type'] ?? null) === 'student') {
            $data['program'] = $data['stream'] ?? null;
        }

        if ($request->hasFile('photo')) {
            $this->deletePhoto($member->photo);
            $data['photo'] = $this->storePhoto($request, $data['roll_number']);
        } elseif ($request->filled('photo_capture')) {
            $this->deletePhoto($member->photo);
            $data['photo'] = $this->storeBase64Photo($request->input('photo_capture'), $data['roll_number']);
        }

        try {
            DB::transaction(function () use ($member, $data, $password, $loginUserId, $deviceId, $hajiriData) {
                $member->update($data);
                $user = app(MemberAccountService::class)->sync($member, $password, $loginUserId);

                if (in_array($member->member_type, ['teacher', 'staff'], true)) {
                    $oldDeviceId = $user->device_id;
                    $newDeviceId = $deviceId ?: null;

                    $user->forceFill(array_merge($hajiriData, [
                        'device_id' => $newDeviceId,
                    ]))->save();

                    if ($oldDeviceId && $newDeviceId && (string) $oldDeviceId !== (string) $newDeviceId) {
                        DB::table('attendacelogs')
                            ->where('user_id', (int) $oldDeviceId)
                            ->update(['user_id' => (int) $newDeviceId]);
                    }
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->withErrors([
                'email' => 'The email address is already in use by another account.',
            ]);
        }

        return redirect()->route('admin.hr.members.index')->with('success', 'Member updated and synced across ERP modules.');
    }

    public function destroy(Student $member)
    {
        DB::transaction(function () use ($member) {
            $linkedUser = $member->user;
            $shouldDeleteLinkedStudentLogin = $member->member_type === 'student'
                && $linkedUser
                && $linkedUser->hasRole('student')
                && ! $linkedUser->isSuperAdmin();

            $this->deletePhoto($member->photo);
            $member->delete();

            if ($shouldDeleteLinkedStudentLogin) {
                $linkedUser->delete();
            }
        });

        return back()->with('success', 'Member removed from HR master.');
    }

    private function validated(Request $request, ?Student $member = null): array
    {
        if ($request->filled('roll_number') && blank($request->input('login_user_id'))) {
            $request->merge(['login_user_id' => $request->input('roll_number')]);
        }

        // Split full_name → first / middle / last before validation
        if ($request->filled('full_name')) {
            $parts = preg_split('/\s+/', trim($request->input('full_name')), 3);
            $request->merge([
                'first_name'  => $parts[0] ?? '',
                'middle_name' => count($parts) === 3 ? $parts[1] : '',
                'last_name'   => count($parts) >= 2 ? end($parts) : ($parts[0] ?? ''),
            ]);
        }

        $memberType = $request->input('member_type');
        $org        = $request->input('organization');
        $stream     = $request->input('stream') ?: null;
        $section    = $request->input('section') ?: null;

        return $request->validate([
            'organization' => ['required', 'string', 'max:100'],
            'member_type' => ['required', Rule::in(['student', 'teacher', 'staff'])],
            'stream' => ['required_if:member_type,student', 'nullable', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],

            // Roll number is unique within (org, member_type, stream, section).
            // A student and a teacher can share the same roll number.
            // Null and empty-string are treated the same for stream/section.
            'roll_number' => [
                'required',
                'string',
                'max:100',
                function ($attr, $value, $fail) use ($org, $memberType, $stream, $section, $member) {
                    $conflict = Student::where('roll_number', $value)
                        ->where('organization', $org)
                        ->where('member_type', $memberType)
                        ->where(function ($q) use ($stream) {
                            $stream
                                ? $q->where('stream', $stream)
                                : $q->where(fn ($q2) => $q2->whereNull('stream')->orWhere('stream', ''));
                        })
                        ->where(function ($q) use ($section) {
                            $section
                                ? $q->where('section', $section)
                                : $q->where(fn ($q2) => $q2->whereNull('section')->orWhere('section', ''));
                        })
                        ->when($member, fn ($q) => $q->where('id', '!=', $member->id))
                        ->first();

                    if ($conflict) {
                        $typeLabel = ucfirst($memberType);
                        $fail("{$typeLabel} ID \"{$value}\" is already taken by {$conflict->full_name}. Please use a different ID.");
                    }
                },
            ],

            // Login user ID (student_code) must be globally unique across all users.
            'login_user_id' => [
                'nullable',
                'string',
                'max:100',
                function ($attr, $value, $fail) use ($member) {
                    if (blank($value)) return;
                    $conflict = User::where('student_code', $value)
                        ->when($member?->user_id, fn ($q) => $q->where('id', '!=', $member->user_id))
                        ->with('student')
                        ->first();
                    if ($conflict) {
                        $name = $conflict->student?->full_name ?? $conflict->name ?? 'another member';
                        $fail("User ID \"{$value}\" is already linked to {$name}. Please choose a different login ID.");
                    }
                },
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_relation' => ['nullable', Rule::in(['father', 'mother', 'grandfather', 'guardian', 'other'])],
            'guardian_contact' => ['nullable', 'string', 'max:30'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'grandfather_name' => ['nullable', 'string', 'max:150'],
            'registration_no' => ['nullable', 'string', 'max:100'],
            'dob' => ['nullable', 'date'],
            'dob_bs' => ['nullable', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'gender' => ['nullable', 'string', 'max:30'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'citizenship_no' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'parent_contact' => ['nullable', 'string', 'max:30'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'designation' => ['nullable', 'string', 'max:100'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'employee_category' => ['nullable', Rule::in(['academic', 'administrative'])],
            'joining_date' => ['nullable', 'date'],
            'joining_date_bs' => ['nullable', 'string', 'max:20'],
            'permanent_date' => ['nullable', 'date'],
            'permanent_date_bs' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_branch' => ['nullable', 'string', 'max:150'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:80'],
            'pan_number' => ['nullable', 'string', 'max:80'],
            'ssf_number' => ['nullable', 'string', 'max:80'],
            'cit_number' => ['nullable', 'string', 'max:80'],
            'valid_till' => ['nullable', 'date'],
            'valid_till_bs' => ['nullable', 'string', 'max:20'],
            'program' => ['nullable', 'string', 'max:100'],
            'batch' => ['nullable', 'string', 'max:20'],
            'zone' => ['nullable', 'string', 'max:50'],
            'district' => ['nullable', 'string', 'max:50'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'permanent_province' => ['nullable', 'string', 'max:100'],
            'permanent_district' => ['nullable', 'string', 'max:100'],
            'permanent_municipality' => ['nullable', 'string', 'max:150'],
            'permanent_ward' => ['nullable', 'string', 'max:20'],
            'permanent_tole' => ['nullable', 'string', 'max:150'],
            'address_en' => ['nullable', 'string', 'max:300'],
            'temporary_province' => ['nullable', 'string', 'max:100'],
            'temporary_district' => ['nullable', 'string', 'max:100'],
            'temporary_municipality' => ['nullable', 'string', 'max:150'],
            'temporary_ward' => ['nullable', 'string', 'max:20'],
            'temporary_tole' => ['nullable', 'string', 'max:150'],
            'bus_route' => ['nullable', 'string', 'max:100'],
            'bus_stop' => ['nullable', 'string', 'max:100'],
            'has_bus_pass' => ['boolean'],
            'library_id' => ['nullable', 'string', 'max:50'],
            'has_library_card' => ['boolean'],
            'password' => [$member ? 'nullable' : 'nullable', 'confirmed', Password::min(8)],
            'device_id' => [
                'nullable',
                'integer',
                'min:1',
                function ($attr, $value, $fail) use ($member) {
                    if (blank($value)) return;
                    $conflict = User::where('device_id', (int) $value)
                        ->when($member?->user_id, fn ($q) => $q->where('id', '!=', $member->user_id))
                        ->with('student')
                        ->first();
                    if ($conflict) {
                        $name = $conflict->student?->full_name ?? $conflict->name ?? 'another member';
                        $fail("Device ID {$value} is already assigned to {$name}. Each device can only be linked to one member.");
                    }
                },
            ],
            'designation_id' => $this->optionalForeignIdRules((new Designation())->getTable()),
            'employment_type_id' => $this->optionalForeignIdRules((new EmploymentType())->getTable()),
            'work_assigned_id' => $this->optionalForeignIdRules((new WorkAssigned())->getTable()),
            'hajiri_department_id' => $this->optionalForeignIdRules((new HajiriDepartment())->getTable()),
        ]);
    }

    // Maps normalised IEMIS/Excel column names to internal field names.
    private array $xlsxColumnMap = [
        'sn'                      => 'roll_number',
        's_n'                     => 'roll_number',
        'roll_number'             => 'roll_number',
        'student_id'              => 'registration_no',
        'registration_no'         => 'registration_no',
        'fullname'                => '_fullname',
        'full_name'               => '_fullname',
        'first_name'              => 'first_name',
        'middle_name'             => 'middle_name',
        'last_name'               => 'last_name',
        'gender'                  => 'gender',
        'father_name'             => 'father_name',
        'mother_name'             => 'mother_name',
        'grandfather_name'        => 'grandfather_name',
        'dob'                     => 'dob_bs',
        'dob_bs'                  => 'dob_bs',
        'permanent_address'       => 'address_en',
        'address_en'              => 'address_en',
        'guardian_name'           => 'guardian_name',
        'guardian_contact_number' => 'guardian_contact',
        'guardian_contact'        => 'guardian_contact',
        'mobile'                  => 'mobile',
        'email'                   => 'email',
        'citizenship_no'          => 'citizenship_no',
        'year'                    => 'batch',
        'batch'                   => 'batch',
        'program'                 => 'program',
        'stream'                  => 'stream',
        'section'                 => 'section',
        'member_type'             => 'member_type',
        'designation'             => 'designation',
        'employment_type'         => 'employment_type',
        'blood_group'             => 'blood_group',
        'parent_contact'          => 'parent_contact',
        'joining_date'            => 'joining_date',
        'permanent_date'          => 'permanent_date',
        'bank_name'               => 'bank_name',
        'bank_branch'             => 'bank_branch',
        'bank_account_name'       => 'bank_account_name',
        'bank_account_number'     => 'bank_account_number',
        'pan_number'              => 'pan_number',
        'device_id'               => 'device_id',
        'password'                => 'password',
        'login_user_id'           => 'login_user_id',
    ];

    public function importForm()
    {
        return view('hr.members.import', [
            'formOptions'   => $this->buildFormOptions(),
            'hajiriOptions' => $this->hajiriOptions(),
        ]);
    }

    // ── CSV Preview ───────────────────────────────────────────────────────
    public function importCsvPreview(Request $request)
    {
        $request->validate([
            'csv_file'           => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'organization'       => ['required', 'string', 'max:100'],
            'member_type'        => ['required', Rule::in(['student', 'teacher', 'staff'])],
            'stream'             => ['required', 'string', 'max:100'],
            'section'            => ['required', 'string', 'max:50'],
            'employee_category'  => ['nullable', Rule::in(['academic', 'administrative'])],
            'designation_id'     => $this->optionalForeignIdRules((new Designation())->getTable()),
            'employment_type_id' => $this->optionalForeignIdRules((new EmploymentType())->getTable()),
            'work_assigned_id'   => $this->optionalForeignIdRules((new WorkAssigned())->getTable()),
            'hajiri_department_id' => $this->optionalForeignIdRules((new HajiriDepartment())->getTable()),
        ]);

        $handle  = fopen($request->file('csv_file')->getRealPath(), 'r');
        $headers = array_map(fn($h) => strtolower(str_replace([' ', '-'], '_', trim($h))), fgetcsv($handle) ?: []);

        $required = ['first_name', 'last_name', 'roll_number'];
        $missing  = array_diff($required, $headers);
        if ($missing) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV is missing columns: ' . implode(', ', $missing)]);
        }

        $rows   = [];
        $lineNo = 1;
        while (($raw = fgetcsv($handle)) !== false) {
            $lineNo++;
            if (count($raw) !== count($headers)) continue;
            $data = array_combine($headers, array_map('trim', $raw));
            if (empty(array_filter($data))) continue;
            $rows[] = $this->buildImportRow($lineNo, $data, $request->organization, $request->stream, $request->section);
        }
        fclose($handle);

        if (empty($rows)) {
            return back()->withErrors(['csv_file' => 'No data rows found.']);
        }

        return $this->showImportPreview($rows, $request);
    }

    // ── Excel / IEMIS Preview ─────────────────────────────────────────────
    public function importXlsxPreview(Request $request)
    {
        $request->validate([
            'xlsx_file'          => ['required', 'file', 'mimes:xlsx,xls,ods,csv,txt', 'max:10240'],
            'organization'       => ['required', 'string', 'max:100'],
            'member_type'        => ['required', Rule::in(['student', 'teacher', 'staff'])],
            'stream'             => ['required', 'string', 'max:100'],
            'section'            => ['required', 'string', 'max:50'],
            'employee_category'  => ['nullable', Rule::in(['academic', 'administrative'])],
            'designation_id'     => $this->optionalForeignIdRules((new Designation())->getTable()),
            'employment_type_id' => $this->optionalForeignIdRules((new EmploymentType())->getTable()),
            'work_assigned_id'   => $this->optionalForeignIdRules((new WorkAssigned())->getTable()),
            'hajiri_department_id' => $this->optionalForeignIdRules((new HajiriDepartment())->getTable()),
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('xlsx_file')->getRealPath());
        } catch (\Throwable $e) {
            return back()->withErrors(['xlsx_file' => 'Could not read file: ' . $e->getMessage()]);
        }

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        if (empty($sheetData)) {
            return back()->withErrors(['xlsx_file' => 'The file is empty.']);
        }

        $rawHeaders = array_shift($sheetData);
        $normalized = array_map(function ($h) {
            $h = strtolower(trim((string) $h));
            $h = str_replace([' ', '-'], '_', $h);
            $h = preg_replace('/[^a-z0-9_]/', '_', $h);
            return preg_replace('/_+/', '_', trim($h, '_'));
        }, $rawHeaders);

        // Build column index → internal field name map
        $fieldMap     = [];
        $hasRollCol   = false;
        foreach ($normalized as $i => $h) {
            $field = $this->xlsxColumnMap[$h] ?? null;
            $fieldMap[$i] = $field;
            if ($field === 'roll_number') $hasRollCol = true;
        }

        $rows     = [];
        $lineNo   = 1;
        $autoRoll = 0;

        foreach ($sheetData as $rawRow) {
            $lineNo++;
            if (empty(array_filter(array_map('strval', $rawRow)))) continue;

            $data = [];
            foreach ($rawRow as $i => $val) {
                $field = $fieldMap[$i] ?? null;
                if (!$field) continue;
                $val = trim((string) $val);

                if ($field === '_fullname') {
                    $parts = preg_split('/\s+/', $val, 3);
                    $data['first_name']  = ucfirst(strtolower($parts[0] ?? ''));
                    $data['last_name']   = ucfirst(strtolower(end($parts)));
                    $data['middle_name'] = count($parts) === 3 ? ucwords(strtolower($parts[1])) : '';
                } else {
                    $data[$field] = $val;
                }
            }

            if (!$hasRollCol || empty($data['roll_number'])) {
                $autoRoll++;
                $data['roll_number'] = (string) $autoRoll;
            }

            $rows[] = $this->buildImportRow($lineNo, $data, $request->organization, $request->stream, $request->section);
        }

        if (empty($rows)) {
            return back()->withErrors(['xlsx_file' => 'No data rows found in the file.']);
        }

        return $this->showImportPreview($rows, $request);
    }

    // ── Confirm Import (shared by CSV and Excel) ──────────────────────────
    public function confirmImport(Request $request)
    {
        $rows    = session('hr_import_rows', []);
        $context = session('hr_import_context');

        if (!$rows || !$context) {
            return redirect()->route('admin.hr.members.import')
                ->withErrors(['error' => 'Session expired. Please re-upload.']);
        }

        $valid   = array_filter($rows, fn($r) => !$r['error']);
        $created = 0;

        DB::transaction(function () use ($valid, $context, &$created) {
            foreach ($valid as $row) {
                $rowOrg     = $row['organization']  ?? $context['organization'];
                $rowStream  = ($row['stream']  ?? '') ?: $context['stream'];
                $rowSection = ($row['section'] ?? '') ?: $context['section'];

                $loginUserId = trim($row['login_user_id'] ?? '')
                    ?: $this->generateLoginId($rowOrg, $rowStream, $rowSection, $row['roll_number']);

                if (Student::where('roll_number', $row['roll_number'])
                        ->where('organization', $rowOrg)
                        ->when($rowStream,  fn ($q) => $q->where('stream',  $rowStream))
                        ->when($rowSection, fn ($q) => $q->where('section', $rowSection))
                        ->exists()
                    || User::where('student_code', $loginUserId)->exists()) {
                    continue;
                }

                $memberType = in_array($row['member_type'] ?? '', ['student', 'teacher', 'staff'], true)
                    ? $row['member_type']
                    : $context['member_type'];

                $dobAd = $row['dob'] ?: ($row['dob_bs'] ? $this->bsToAdDate($row['dob_bs']) : null);

                $member = Student::create([
                    'organization'        => $row['organization'] ?? $context['organization'],
                    'member_type'         => $memberType,
                    'stream'              => ($row['stream'] ?? '') ?: $context['stream'],
                    'section'             => ($row['section'] ?? '') ?: $context['section'],
                    'roll_number'         => $row['roll_number'],
                    'registration_no'     => $row['registration_no']     ?: null,
                    'first_name'          => $row['first_name'],
                    'middle_name'         => $row['middle_name']          ?: null,
                    'last_name'           => $row['last_name'],
                    'gender'              => $row['gender']               ?: null,
                    'dob'                 => $dobAd,
                    'dob_bs'              => $row['dob_bs']               ?: null,
                    'mobile'              => $row['mobile']               ?: null,
                    'email'               => $row['email']                ?: null,
                    'father_name'         => $row['father_name']          ?: null,
                    'mother_name'         => $row['mother_name']          ?: null,
                    'grandfather_name'    => $row['grandfather_name']     ?: null,
                    'guardian_name'       => $row['guardian_name']        ?: null,
                    'guardian_contact'    => $row['guardian_contact']     ?: null,
                    'parent_contact'      => $row['parent_contact']       ?: null,
                    'address_en'          => $row['address_en']           ?: null,
                    'citizenship_no'      => $row['citizenship_no']       ?: null,
                    'blood_group'         => $row['blood_group']          ?: null,
                    'designation'         => $row['designation']          ?: null,
                    'employment_type'     => $row['employment_type']      ?: null,
                    'employee_category'   => $context['employee_category'] ?: null,
                    'joining_date'        => blank($row['joining_date'] ?? null) ? null : $row['joining_date'],
                    'permanent_date'      => blank($row['permanent_date'] ?? null) ? null : $row['permanent_date'],
                    'bank_name'           => $row['bank_name']            ?: null,
                    'bank_branch'         => $row['bank_branch']          ?: null,
                    'bank_account_name'   => $row['bank_account_name']   ?: null,
                    'bank_account_number' => $row['bank_account_number'] ?: null,
                    'pan_number'          => $row['pan_number']           ?: null,
                    'program'             => $memberType === 'student'
                        ? (($row['stream'] ?? '') ?: $context['stream'])
                        : ($row['program'] ?? null),
                    'has_bus_pass'        => false,
                    'has_library_card'    => false,
                ]);

                $user = app(MemberAccountService::class)->sync($member, $row['password'] ?: null, $loginUserId);

                if (in_array($memberType, ['teacher', 'staff'], true)) {
                    $user->forceFill([
                        'device_id'            => $row['device_id']            ?: null,
                        'designation_id'       => $context['designation_id']   ?: null,
                        'employment_type_id'   => $context['employment_type_id'] ?: null,
                        'work_assigned_id'     => $context['work_assigned_id'] ?: null,
                        'hajiri_department_id' => $context['hajiri_department_id'] ?: null,
                    ])->save();
                }

                $created++;
            }
        });

        session()->forget(['hr_import_rows', 'hr_import_context']);

        return redirect()->route('admin.hr.members.index')
            ->with('success', "{$created} members imported and synced.");
    }

    // ── Photo ZIP Preview ─────────────────────────────────────────────────
    public function importPhotosPreview(Request $request)
    {
        $request->validate([
            'zip_file'     => ['required', 'file', 'mimes:zip', 'max:102400'],
            'organization' => ['required', 'string', 'max:100'],
            'stream'       => ['nullable', 'string', 'max:100'],
            'section'      => ['nullable', 'string', 'max:50'],
        ]);

        $org     = $request->organization;
        $stream  = $request->stream ?: null;
        $section = $request->section ?: null;

        $students = Student::where('organization', $org)
            ->when($stream,  fn($q) => $q->where('stream', $stream))
            ->when($section, fn($q) => $q->where('section', $section))
            ->get()->keyBy('roll_number');

        if ($students->isEmpty()) {
            return back()->withErrors(['zip_file' => 'No members found for the selected filters.']);
        }

        $uuid    = Str::uuid()->toString();
        $tempDir = public_path("temp_photos/{$uuid}");
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        $zip     = new ZipArchive();
        if ($zip->open($request->file('zip_file')->getRealPath()) !== true) {
            return back()->withErrors(['zip_file' => 'Could not open ZIP file.']);
        }

        $imageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $photoRows = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_ends_with($name, '/') || str_starts_with(basename($name), '.')) continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $imageExts)) continue;

            $roll = pathinfo(basename($name), PATHINFO_FILENAME);
            file_put_contents("{$tempDir}/{$roll}.{$ext}", $zip->getFromIndex($i));

            $student     = $students->get($roll);
            $photoRows[] = [
                'roll'          => $roll,
                'filename'      => basename($name),
                'ext'           => $ext,
                'temp_url'      => asset("temp_photos/{$uuid}/{$roll}.{$ext}"),
                'student_id'    => $student?->id,
                'student_name'  => $student?->full_name,
                'current_photo' => $student?->photo_url,
                'has_photo'     => $student && !empty($student->photo),
                'action'        => $student ? ($student->photo ? 'replace' : 'add') : 'no_match',
            ];
        }
        $zip->close();

        usort($photoRows, fn($a, $b) => strcmp(
            ['add' => 0, 'replace' => 1, 'no_match' => 2][$a['action']],
            ['add' => 0, 'replace' => 1, 'no_match' => 2][$b['action']]
        ));

        $photoContext = compact('org', 'stream', 'section', 'uuid');
        session(['hr_photo_rows' => $photoRows, 'hr_photo_context' => $photoContext]);

        return view('hr.members.import', [
            'photoRows'     => $photoRows,
            'photoContext'  => $photoContext,
            'formOptions'   => $this->buildFormOptions(),
            'hajiriOptions' => $this->hajiriOptions(),
        ]);
    }

    // ── Photo ZIP Confirm ─────────────────────────────────────────────────
    public function importPhotosConfirm(Request $request)
    {
        $photoRows    = session('hr_photo_rows', []);
        $photoContext = session('hr_photo_context');

        if (!$photoRows || !$photoContext) {
            return redirect()->route('admin.hr.members.import')
                ->withErrors(['error' => 'Session expired. Please re-upload the ZIP.']);
        }

        $uuid    = $photoContext['uuid'];
        $tempDir = public_path("temp_photos/{$uuid}");
        $destDir = public_path('photos');
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);

        $skip     = array_flip($request->input('skip', []));
        $added    = 0;
        $replaced = 0;

        foreach ($photoRows as $row) {
            if ($row['action'] === 'no_match') continue;
            if (isset($skip[$row['roll']])) continue;

            $tempFile = "{$tempDir}/{$row['roll']}.{$row['ext']}";
            if (!file_exists($tempFile)) continue;

            $destFile = "{$destDir}/{$row['roll']}.{$row['ext']}";
            copy($tempFile, $destFile);
            Student::where('id', $row['student_id'])->update(['photo' => "photos/{$row['roll']}.{$row['ext']}"]);
            $row['action'] === 'replace' ? $replaced++ : $added++;
        }

        if (is_dir($tempDir)) File::deleteDirectory($tempDir);
        session()->forget(['hr_photo_rows', 'hr_photo_context']);

        return redirect()->route('admin.hr.members.index')
            ->with('success', "{$added} photo(s) added, {$replaced} replaced.");
    }

    // ── Shared helpers ────────────────────────────────────────────────────
    private function buildImportRow(int $lineNo, array $data, string $defaultOrg, ?string $defaultStream, ?string $defaultSection): array
    {
        $dob    = $this->normalizeDate($data['dob'] ?? '');
        $dobBs  = $data['dob_bs'] ?? '';
        $mobile = ($data['mobile'] ?? '') ?: ($data['guardian_contact'] ?? '');

        $rollNumber = $data['roll_number'] ?? '';

        $effectiveStream  = ($data['stream']  ?? '') ?: ($defaultStream  ?? '');
        $effectiveSection = ($data['section'] ?? '') ?: ($defaultSection ?? '');

        $loginUserId = trim($data['login_user_id'] ?? '')
            ?: $this->generateLoginId($defaultOrg, $effectiveStream, $effectiveSection, $rollNumber);

        $rollExists = $rollNumber && Student::where('roll_number', $rollNumber)
            ->where('organization', $defaultOrg)
            ->when($effectiveStream,  fn ($q) => $q->where('stream',  $effectiveStream))
            ->when($effectiveSection, fn ($q) => $q->where('section', $effectiveSection))
            ->exists();
        $loginExists = $loginUserId && User::where('student_code', $loginUserId)->exists();

        $error = null;
        if (empty($rollNumber))          $error = 'Roll number is empty';
        elseif (empty($data['first_name'])) $error = 'First name is empty';
        elseif (empty($data['last_name']))  $error = 'Last name is empty';
        elseif ($rollExists)             $error = "Roll #{$rollNumber} already exists";
        elseif ($loginExists)            $error = "Login ID '{$loginUserId}' already taken";

        return [
            'line'             => $lineNo,
            'roll_number'      => $rollNumber,
            'login_user_id'    => $loginUserId,
            'first_name'       => $data['first_name']       ?? '',
            'middle_name'      => $data['middle_name']      ?? '',
            'last_name'        => $data['last_name']        ?? '',
            'gender'           => $data['gender']           ?? '',
            'dob'              => $dob ?? ($data['dob']     ?? ''),
            'dob_bs'           => $dobBs,
            'mobile'           => $mobile,
            'email'            => $data['email']            ?? '',
            'father_name'      => $data['father_name']      ?? '',
            'mother_name'      => $data['mother_name']      ?? '',
            'grandfather_name' => $data['grandfather_name'] ?? '',
            'guardian_name'    => $data['guardian_name']    ?? '',
            'guardian_contact' => $data['guardian_contact'] ?? '',
            'parent_contact'   => $data['parent_contact']   ?? '',
            'address_en'       => $data['address_en']       ?? '',
            'registration_no'  => $data['registration_no']  ?? '',
            'citizenship_no'   => $data['citizenship_no']   ?? '',
            'blood_group'      => $data['blood_group']      ?? '',
            'member_type'      => $data['member_type']      ?? '',
            'stream'           => $data['stream']           ?? '',
            'section'          => $data['section']          ?? '',
            'designation'      => $data['designation']      ?? '',
            'employment_type'  => $data['employment_type']  ?? '',
            'joining_date'     => $data['joining_date']     ?? '',
            'permanent_date'   => $data['permanent_date']   ?? '',
            'bank_name'        => $data['bank_name']        ?? '',
            'bank_branch'      => $data['bank_branch']      ?? '',
            'bank_account_name'   => $data['bank_account_name']   ?? '',
            'bank_account_number' => $data['bank_account_number'] ?? '',
            'pan_number'       => $data['pan_number']       ?? '',
            'device_id'        => $data['device_id']        ?? '',
            'program'          => $data['program']          ?? '',
            'batch'            => $data['batch']            ?? '',
            'password'         => $data['password']         ?? '',
            'organization'     => $defaultOrg,
            'error'            => $error,
        ];
    }

    private function showImportPreview(array $rows, Request $request): \Illuminate\View\View
    {
        $context = [
            'organization'         => $request->organization,
            'member_type'          => $request->member_type,
            'stream'               => $request->stream,
            'section'              => $request->section,
            'employee_category'    => $request->employee_category,
            'designation_id'       => $request->designation_id,
            'employment_type_id'   => $request->employment_type_id,
            'work_assigned_id'     => $request->work_assigned_id,
            'hajiri_department_id' => $request->hajiri_department_id,
        ];

        session(['hr_import_rows' => $rows, 'hr_import_context' => $context]);

        return view('hr.members.import', [
            'rows'          => $rows,
            'context'       => $context,
            'formOptions'   => $this->buildFormOptions(),
            'hajiriOptions' => $this->hajiriOptions(),
        ]);
    }

    private function bsToAdDate(string $bsDate): ?string
    {
        if (!preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', trim($bsDate), $m)) return null;
        [$bsYear, $bsMonth, $bsDay] = [(int)$m[1], (int)$m[2], (int)$m[3]];
        if ($bsMonth <= 9) { $adYear = $bsYear - 57; $adMonth = $bsMonth + 3; }
        else               { $adYear = $bsYear - 56; $adMonth = $bsMonth - 9; }
        try {
            $maxDay = (int) Carbon::createFromDate($adYear, $adMonth, 1)->endOfMonth()->day;
            return sprintf('%04d-%02d-%02d', $adYear, $adMonth, min($bsDay, $maxDay));
        } catch (\Throwable) { return null; }
    }

    private function generateLoginId(string $org, string $stream, string $section, string $roll): string
    {
        // Org slug → abbreviation: "barchhain-secondary-school" → "BSS"
        $orgCode = strtoupper(implode('', array_map(
            fn ($w) => $w[0] ?? '',
            preg_split('/[-_ ]+/', $org)
        )));
        if (\strlen($orgCode) < 2) {
            $orgCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $org), 0, 4));
        }

        // Unambiguous charset — no O/0 or I/1 confusion when read on paper
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $len   = \strlen($chars);

        do {
            $random = '';
            for ($i = 0; $i < 6; $i++) {
                $random .= $chars[random_int(0, $len - 1)];
            }
            $id = $orgCode . '-' . $random;
        } while (User::where('student_code', $id)->exists());

        return $id;
    }

    private function normalizeDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') return null;
        foreach (['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d'] as $fmt) {
            try { return Carbon::createFromFormat($fmt, $value)->format('Y-m-d'); } catch (\Throwable) {}
        }
        try { return Carbon::parse($value)->format('Y-m-d'); } catch (\Throwable) { return null; }
    }

    public function template()
    {
        $headers = [
            'roll_number', 'login_user_id', 'member_type', 'first_name', 'middle_name', 'last_name',
            'father_name', 'mother_name', 'grandfather_name', 'guardian_name',
            'guardian_relation', 'guardian_contact',
            'dob', 'gender', 'blood_group', 'mobile', 'parent_contact', 'email',
            'stream', 'section', 'designation', 'employment_type', 'employee_category',
            'joining_date', 'permanent_date', 'device_id', 'bank_name', 'bank_branch',
            'bank_account_name', 'bank_account_number', 'pan_number',
            'permanent_province', 'permanent_district', 'permanent_municipality',
            'temporary_province', 'temporary_district', 'temporary_municipality',
            'password',
        ];

        return response(implode(',', $headers) . "\n", 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="hr-members-template.csv"',
        ]);
    }

    private function hajiriOptions(): array
    {
        return [
            'designations' => Designation::orderBy('label')->get(),
            'employmentTypes' => EmploymentType::orderBy('label')->get(),
            'workAssigned' => WorkAssigned::orderBy('label')->get(),
            'departments' => HajiriDepartment::orderBy('label')->get(),
        ];
    }

    private function hajiriData(Request $request): array
    {
        return [
            'designation_id' => $request->input('designation_id'),
            'employment_type_id' => $request->input('employment_type_id'),
            'work_assigned_id' => $request->input('work_assigned_id'),
            'hajiri_department_id' => $request->input('hajiri_department_id'),
        ];
    }

    private function optionalForeignIdRules(string $table): array
    {
        return array_filter([
            'nullable',
            'integer',
            Schema::hasTable($table) ? Rule::exists($table, 'id') : null,
        ]);
    }

    private function buildFormOptions(): array
    {
        $options = [];

        if (Schema::hasTable('organizations') && Schema::hasTable('departments') && Schema::hasTable('sections')) {
            $options = CardOrganization::query()
                ->with(['departments.sections'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->mapWithKeys(function (CardOrganization $organization) {
                    return [
                        $organization->slug => [
                            'label' => $organization->name,
                            'streams' => $organization->departments
                                ->where('is_active', true)
                                ->mapWithKeys(fn ($department) => [
                                    $department->name => $department->sections
                                        ->where('is_active', true)
                                        ->pluck('name')
                                        ->values()
                                        ->all(),
                                ])
                                ->all(),
                        ],
                    ];
                })
                ->all();
        }

        $existingOptions = Student::query()
            ->select('organization', 'stream', 'section')
            ->whereNotNull('organization')
            ->where('organization', '!=', '')
            ->orderBy('organization')
            ->orderBy('stream')
            ->orderBy('section')
            ->get()
            ->groupBy('organization')
            ->map(fn (Collection $rows, string $organization) => [
                'label' => ucwords(str_replace(['-', '_'], ' ', $organization)),
                'streams' => $rows->groupBy('stream')
                    ->map(fn (Collection $streamRows) => $streamRows->pluck('section')->filter()->unique()->values()->all())
                    ->all(),
            ])
            ->all();

        foreach ($existingOptions as $organization => $organizationData) {
            $options[$organization]['label'] ??= $organizationData['label'];

            foreach ($organizationData['streams'] ?? [] as $stream => $sections) {
                if (blank($stream)) {
                    continue;
                }

                $currentSections = $options[$organization]['streams'][$stream] ?? [];
                $options[$organization]['streams'][$stream] = collect($currentSections)
                    ->merge($sections)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        ksort($options);

        return $options;
    }

    private function storePhoto(Request $request, string $code): string
    {
        File::ensureDirectoryExists(public_path('photos'));
        $file = $request->file('photo');
        $filename = $code . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('photos'), $filename);

        return "photos/{$filename}";
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && str_starts_with($path, 'photos/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    private function storeBase64Photo(string $base64, string $code): string
    {
        // Strip data URI prefix: "data:image/jpeg;base64,..."
        $data     = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $decoded  = base64_decode($data);
        if ($decoded === false) {
            return '';
        }
        File::ensureDirectoryExists(public_path('photos'));
        $filename = $code . '_' . time() . '.jpg';
        File::put(public_path("photos/{$filename}"), $decoded);
        return "photos/{$filename}";
    }

    public function getMunicipalitiesByDistrict($district)
    {
        $municipalities = Student::query()
            ->where('permanent_district', $district)
            ->whereNotNull('permanent_municipality')
            ->where('permanent_municipality', '!=', '')
            ->distinct()
            ->orderBy('permanent_municipality')
            ->pluck('permanent_municipality');

        return response()->json($municipalities->values());
    }
}
