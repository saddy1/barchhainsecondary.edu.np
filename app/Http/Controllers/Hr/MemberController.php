<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Card\Organization;
use App\Models\Card\Student;
use App\Models\User;
use App\Models\Hajiri\Department as HajiriDepartment;
use App\Models\Hajiri\Designation;
use App\Models\Hajiri\EmploymentType;
use App\Models\Hajiri\WorkAssigned;
use App\Services\MemberAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = Student::query()
            ->with('user.roles')
            ->when($request->filled('type'), fn ($query) => $query->where('member_type', $request->type))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('roll_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

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

        return view('hr.members.index', compact('members', 'counts', 'orphanUsers'));
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
        }

        DB::transaction(function () use ($member, $data, $password, $loginUserId, $deviceId, $hajiriData) {
            $member->update($data);
            $user = app(MemberAccountService::class)->sync($member, $password, $loginUserId);

            if (in_array($member->member_type, ['teacher', 'staff'], true)) {
                $user->forceFill(array_merge($hajiriData, [
                    'device_id' => $deviceId ?: null,
                ]))->save();
            }
        });

        return redirect()->route('admin.hr.members.index')->with('success', 'Member updated and synced across ERP modules.');
    }

    public function destroy(Student $member)
    {
        $this->deletePhoto($member->photo);
        $member->delete();

        return back()->with('success', 'Member removed from HR master.');
    }

    private function validated(Request $request, ?Student $member = null): array
    {
        if ($request->filled('roll_number') && blank($request->input('login_user_id'))) {
            $request->merge(['login_user_id' => $request->input('roll_number')]);
        }

        return $request->validate([
            'organization' => ['required', 'string', 'max:100'],
            'member_type' => ['required', Rule::in(['student', 'teacher', 'staff'])],
            'stream' => ['required', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'roll_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('students', 'roll_number')->ignore($member?->id),
            ],
            'login_user_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('users', 'student_code')->ignore($member?->user_id),
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
            'permanent_date' => ['nullable', 'date'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_branch' => ['nullable', 'string', 'max:150'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:80'],
            'pan_number' => ['nullable', 'string', 'max:80'],
            'ssf_number' => ['nullable', 'string', 'max:80'],
            'cit_number' => ['nullable', 'string', 'max:80'],
            'valid_till' => ['nullable', 'date'],
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
            'device_id' => ['nullable', 'integer', 'min:1', Rule::unique('users', 'device_id')->ignore($member?->user_id)],
            'designation_id' => ['nullable', 'integer', 'exists:designations,id'],
            'employment_type_id' => ['nullable', 'integer', 'exists:employment_types,id'],
            'work_assigned_id' => ['nullable', 'integer', 'exists:work_assigneds,id'],
            'hajiri_department_id' => ['nullable', 'integer', 'exists:hajiri_departments,id'],
        ]);
    }

    public function importForm()
    {
        return view('hr.members.import', [
            'formOptions' => $this->buildFormOptions(),
            'hajiriOptions' => $this->hajiriOptions(),
        ]);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'organization' => ['required', 'string', 'max:100'],
            'member_type' => ['required', Rule::in(['student', 'teacher', 'staff'])],
            'stream' => ['nullable', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'employee_category' => ['nullable', Rule::in(['academic', 'administrative'])],
            'designation_id' => ['nullable', 'integer', 'exists:designations,id'],
            'employment_type_id' => ['nullable', 'integer', 'exists:employment_types,id'],
            'work_assigned_id' => ['nullable', 'integer', 'exists:work_assigneds,id'],
            'hajiri_department_id' => ['nullable', 'integer', 'exists:hajiri_departments,id'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $headers = array_map(fn ($header) => strtolower(str_replace([' ', '-'], '_', trim($header))), fgetcsv($handle) ?: []);

        $required = ['roll_number', 'first_name', 'last_name'];
        $missing = array_diff($required, $headers);
        if ($missing) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV is missing columns: ' . implode(', ', $missing)]);
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $rows[] = array_combine($headers, array_map('trim', $row));
        }
        fclose($handle);

        $created = 0;
        DB::transaction(function () use ($rows, $request, &$created) {
            foreach ($rows as $row) {
                if (blank($row['roll_number'] ?? null) || blank($row['first_name'] ?? null) || blank($row['last_name'] ?? null)) {
                    continue;
                }

                $loginUserId = trim($row['login_user_id'] ?? '') ?: $row['roll_number'];

                if (Student::where('roll_number', $row['roll_number'])->exists() || \App\Models\User::where('student_code', $loginUserId)->exists()) {
                    continue;
                }

                $memberType = $row['member_type'] ?? $request->member_type;
                if (! in_array($memberType, ['student', 'teacher', 'staff'], true)) {
                    $memberType = $request->member_type;
                }

                $member = Student::create([
                    'organization' => $row['organization'] ?? $request->organization,
                    'member_type' => $memberType,
                    'stream' => $row['stream'] ?? $request->stream,
                    'section' => $row['section'] ?? $request->section,
                    'roll_number' => $row['roll_number'],
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?? null,
                    'last_name' => $row['last_name'],
                    'father_name' => $row['father_name'] ?? null,
                    'mother_name' => $row['mother_name'] ?? null,
                    'grandfather_name' => $row['grandfather_name'] ?? null,
                    'guardian_name' => $row['guardian_name'] ?? null,
                    'guardian_relation' => $row['guardian_relation'] ?? null,
                    'guardian_contact' => $row['guardian_contact'] ?? $row['parent_contact'] ?? null,
                    'dob' => blank($row['dob'] ?? null) ? null : $row['dob'],
                    'gender' => $row['gender'] ?? null,
                    'blood_group' => $row['blood_group'] ?? null,
                    'mobile' => $row['mobile'] ?? null,
                    'parent_contact' => $row['parent_contact'] ?? null,
                    'email' => $row['email'] ?? null,
                    'designation' => $row['designation'] ?? null,
                    'employment_type' => $row['employment_type'] ?? null,
                    'employee_category' => $row['employee_category'] ?? $request->employee_category,
                    'joining_date' => blank($row['joining_date'] ?? null) ? null : $row['joining_date'],
                    'permanent_date' => blank($row['permanent_date'] ?? null) ? null : $row['permanent_date'],
                    'bank_name' => $row['bank_name'] ?? null,
                    'bank_branch' => $row['bank_branch'] ?? null,
                    'bank_account_name' => $row['bank_account_name'] ?? null,
                    'bank_account_number' => $row['bank_account_number'] ?? null,
                    'pan_number' => $row['pan_number'] ?? null,
                    'permanent_province' => $row['permanent_province'] ?? null,
                    'permanent_district' => $row['permanent_district'] ?? null,
                    'permanent_municipality' => $row['permanent_municipality'] ?? null,
                    'temporary_province' => $row['temporary_province'] ?? null,
                    'temporary_district' => $row['temporary_district'] ?? null,
                    'temporary_municipality' => $row['temporary_municipality'] ?? null,
                    'program' => ($memberType === 'student') ? ($row['stream'] ?? $request->stream) : ($row['program'] ?? null),
                    'has_bus_pass' => false,
                    'has_library_card' => false,
                ]);

                $user = app(MemberAccountService::class)->sync($member, $row['password'] ?? null, $loginUserId);

                if (in_array($memberType, ['teacher', 'staff'], true)) {
                    $user->forceFill([
                        'device_id' => $row['device_id'] ?? null,
                        'designation_id' => $request->designation_id,
                        'employment_type_id' => $request->employment_type_id,
                        'work_assigned_id' => $request->work_assigned_id,
                        'hajiri_department_id' => $request->hajiri_department_id,
                    ])->save();
                }

                $created++;
            }
        });

        return redirect()->route('admin.hr.members.index')->with('success', "{$created} members imported and synced.");
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

    private function buildFormOptions(): array
    {
        $user = auth()->user();

        if (Schema::hasTable('organizations') && Schema::hasTable('departments') && Schema::hasTable('sections')) {
            return Organization::query()
                ->where('is_active', true)
                ->when(! $user->isSuperAdmin(), fn ($query) => $query->where('slug', $user->organizationSlug()))
                ->with(['activeDepartments.activeSections'])
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn ($organization) => [
                    $organization->slug => [
                        'label' => $organization->name,
                        'streams' => $organization->activeDepartments
                            ->when(! $user->isSuperAdmin() && $user->departmentName(), fn ($departments) => $departments->where('name', $user->departmentName()))
                            ->mapWithKeys(fn ($department) => [
                                $department->name => $department->activeSections->pluck('name')->values()->all(),
                            ])
                            ->all(),
                    ],
                ])
                ->all();
        }

        return Student::query()
            ->select('organization', 'stream', 'section')
            ->whereNotNull('organization')
            ->where('organization', '!=', '')
            ->orderBy('organization')
            ->orderBy('stream')
            ->orderBy('section')
            ->get()
            ->groupBy('organization')
            ->map(fn (Collection $rows, string $organization) => [
                'label' => ucfirst($organization),
                'streams' => $rows->groupBy('stream')
                    ->map(fn (Collection $streamRows) => $streamRows->pluck('section')->filter()->unique()->values()->all())
                    ->all(),
            ])
            ->all();
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
}
