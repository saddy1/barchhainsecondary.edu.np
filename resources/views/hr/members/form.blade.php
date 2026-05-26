@extends('hr.layouts.app')

@section('title', $member ? 'Edit HR Member' : 'New HR Member')

@section('content')
@php
    $isEdit = (bool) $member;
    $p      = $prefillUser ?? null;  // Hajiri user being linked to HR

    // Split the user's single name field into parts for prefill
    if ($p) {
        $nameParts    = preg_split('/\s+/', trim($p->name), 3);
        $pfFirst      = $nameParts[0] ?? '';
        $pfMiddle     = count($nameParts) === 3 ? $nameParts[1] : '';
        $pfLast       = count($nameParts) >= 2 ? end($nameParts) : '';
        $pfType       = $p->roles->firstWhere('name', 'teacher') ? 'teacher' : 'staff';
    } else {
        $pfFirst = $pfMiddle = $pfLast = $pfType = '';
    }

    $selectedType         = old('member_type',  $isEdit ? $member->member_type  : ($pfType ?: 'student'));
    $selectedOrganization = old('organization',  $isEdit ? $member->organization : array_key_first($formOptions));
    $selectedStream       = old('stream',        $isEdit ? $member->stream       : '');
    $selectedSection      = old('section',       $isEdit ? $member->section      : '');
    $input = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15';
    $label = 'block text-xs font-extrabold uppercase tracking-widest text-gray-500 mb-1.5';
@endphp

<div class="space-y-6" x-data="hrMemberForm()">
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
        <p class="text-sm font-bold uppercase tracking-widest text-white/50">Human Resource</p>
        <h1 class="mt-1 text-3xl font-extrabold">{{ $isEdit ? 'Edit Member' : 'New Member' }}</h1>
        <p class="mt-2 max-w-3xl text-sm font-medium text-white/70">
            Choose the member type first. The form then shows only the fields needed for student or employee records.
        </p>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    @if($p)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-200 text-sm font-extrabold text-amber-900">
                {{ strtoupper(substr($p->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-extrabold text-amber-900">Pre-filled from Hajiri account: {{ $p->name }}</p>
                <p class="text-xs font-medium text-amber-700">Review all fields and fill in any missing information before saving.</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('admin.hr.members.update', $member) : route('admin.hr.members.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Step 1</p>
                    <h2 class="mt-1 text-lg font-extrabold text-gray-950">Categorize Member</h2>
                    <p class="mt-1 text-sm font-medium text-gray-500">This decides where the record appears later: ID Card, Learning, Hajiri, Library, and future modules.</p>
                </div>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <label class="cursor-pointer">
                    <input type="radio" name="member_type" value="student" x-model="memberType" class="peer sr-only">
                    <span class="flex min-h-20 flex-col justify-center rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-extrabold text-gray-600 transition peer-checked:border-[#1a5632] peer-checked:bg-emerald-50 peer-checked:text-[#1a5632]">
                        Student
                        <small class="mt-1 text-xs font-semibold opacity-70">Class, section, roll, guardian</small>
                    </span>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="member_type" value="teacher" x-model="memberType" class="peer sr-only">
                    <span class="flex min-h-20 flex-col justify-center rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-extrabold text-gray-600 transition peer-checked:border-[#1a5632] peer-checked:bg-emerald-50 peer-checked:text-[#1a5632]">
                        Academic Employee
                        <small class="mt-1 text-xs font-semibold opacity-70">Teacher, resources, Hajiri</small>
                    </span>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="member_type" value="staff" x-model="memberType" class="peer sr-only">
                    <span class="flex min-h-20 flex-col justify-center rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-extrabold text-gray-600 transition peer-checked:border-[#1a5632] peer-checked:bg-emerald-50 peer-checked:text-[#1a5632]">
                        Administrative Employee
                        <small class="mt-1 text-xs font-semibold opacity-70">Office staff, Hajiri, payroll</small>
                    </span>
                </label>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="{{ $label }}">Organization</label>
                    <select name="organization" x-model="organization" class="{{ $input }}">
                        @foreach($formOptions as $slug => $organization)
                            <option value="{{ $slug }}">{{ $organization['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}" x-text="isStudent ? 'Class' : 'Department / Unit'"></label>
                    <select name="stream" x-model="stream" required class="{{ $input }}">
                        <option value="">Select</option>
                        <template x-for="streamName in streams" :key="streamName">
                            <option :value="streamName" x-text="streamName"></option>
                        </template>
                    </select>
                </div>
                <div x-show="isStudent" x-transition>
                    <label class="{{ $label }}">Section</label>
                    <select name="section" x-model="section" :required="isStudent" class="{{ $input }}">
                        <option value="">Select</option>
                        <template x-for="sectionName in sections" :key="sectionName">
                            <option :value="sectionName" x-text="sectionName"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}" x-text="isStudent ? 'Roll Number / Student ID' : 'Employee ID'"></label>
                    <input name="roll_number" value="{{ old('roll_number', $isEdit ? $member->roll_number : '') }}" required class="{{ $input }}">
                </div>
                <input type="hidden" name="employee_category" :value="memberType === 'teacher' ? 'academic' : (memberType === 'staff' ? 'administrative' : '')">
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Step 2</p>
            <h2 class="mt-1 text-lg font-extrabold text-gray-950">Personal Details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><label class="{{ $label }}">First Name</label><input name="first_name" value="{{ old('first_name', $isEdit ? $member->first_name : $pfFirst) }}" required class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Middle Name</label><input name="middle_name" value="{{ old('middle_name', $isEdit ? $member->middle_name : $pfMiddle) }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Last Name</label><input name="last_name" value="{{ old('last_name', $isEdit ? $member->last_name : $pfLast) }}" required class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Photo</label><input type="file" name="photo" accept="image/*" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#1a5632] file:px-4 file:py-2 file:text-sm file:font-bold file:text-white"></div>
                <div><label class="{{ $label }}">Date of Birth</label><input type="date" name="dob" value="{{ old('dob', $isEdit ? $member->dob?->format('Y-m-d') : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Gender</label><select name="gender" class="{{ $input }}"><option value="">Select</option>@foreach(['Male','Female','Other'] as $g)<option value="{{ $g }}" @selected(old('gender', $isEdit ? $member->gender : '') === $g)>{{ $g }}</option>@endforeach</select></div>
                <div><label class="{{ $label }}">Blood Group</label><input name="blood_group" value="{{ old('blood_group', $isEdit ? $member->blood_group : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Citizenship No.</label><input name="citizenship_no" value="{{ old('citizenship_no', $isEdit ? $member->citizenship_no : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Mobile</label><input name="mobile" value="{{ old('mobile', $isEdit ? $member->mobile : ($p?->phone ?? '')) }}" class="{{ $input }}"></div>
                <div class="xl:col-span-2"><label class="{{ $label }}">Email</label><input type="email" name="email" value="{{ old('email', $isEdit ? $member->email : ($p?->email ?? '')) }}" class="{{ $input }}"></div>
            </div>
        </section>

        <section x-show="isStudent" x-transition class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <fieldset :disabled="!isStudent">
            <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Student</p>
            <h2 class="mt-1 text-lg font-extrabold text-gray-950">Parent & Guardian Details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><label class="{{ $label }}">Father Name</label><input name="father_name" x-model="fatherName" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Mother Name</label><input name="mother_name" x-model="motherName" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Parent Contact</label><input name="parent_contact" x-model="parentContact" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Grandfather Name</label><input name="grandfather_name" value="{{ old('grandfather_name', $isEdit ? $member->grandfather_name : '') }}" class="{{ $input }}"></div>
                <div>
                    <label class="{{ $label }}">Guardian</label>
                    <select name="guardian_relation" x-model="guardianRelation" class="{{ $input }}">
                        <option value="father">Father</option>
                        <option value="mother">Mother</option>
                        <option value="grandfather">Grandfather</option>
                        <option value="guardian">Other Guardian</option>
                    </select>
                </div>
                <div x-show="guardianRelation === 'guardian'" x-transition>
                    <label class="{{ $label }}">Guardian Name</label>
                    <input x-model="customGuardianName" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">Guardian Contact</label>
                    <input name="guardian_contact" x-model="guardianContact" class="{{ $input }}">
                </div>
                <input type="hidden" name="guardian_name" :value="guardianName">
                <div><label class="{{ $label }}">Registration No.</label><input name="registration_no" value="{{ old('registration_no', $isEdit ? $member->registration_no : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Valid Till</label><input type="date" name="valid_till" value="{{ old('valid_till', $isEdit ? $member->valid_till?->format('Y-m-d') : '') }}" class="{{ $input }}"></div>
            </div>
            </fieldset>
        </section>

        <section x-show="isEmployee" x-transition class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <fieldset :disabled="!isEmployee">
            <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Employee</p>
            <h2 class="mt-1 text-lg font-extrabold text-gray-950">Employment, Hajiri & Payroll</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><label class="{{ $label }}">Father Name</label><input name="father_name" x-model="fatherName" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Grandfather Name</label><input name="grandfather_name" value="{{ old('grandfather_name', $isEdit ? $member->grandfather_name : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Designation Text</label><input name="designation" value="{{ old('designation', $isEdit ? $member->designation : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Employment Type Text</label><input name="employment_type" value="{{ old('employment_type', $isEdit ? $member->employment_type : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Joining Date</label><input type="date" name="joining_date" value="{{ old('joining_date', $isEdit ? $member->joining_date?->format('Y-m-d') : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Permanent Date</label><input type="date" name="permanent_date" value="{{ old('permanent_date', $isEdit ? $member->permanent_date?->format('Y-m-d') : '') }}" class="{{ $input }}"></div>
                <div>
                    <label class="{{ $label }}">Hajiri Device ID</label>
                    <input type="number" name="device_id"
                           value="{{ old('device_id', $isEdit ? $member->user?->device_id : ($p?->device_id ?? '')) }}"
                           placeholder="Must be unique per device"
                           class="{{ $input }}">
                    <p class="mt-1 text-xs font-medium text-gray-400">Each device ID can only be assigned to one person.</p>
                </div>
                <div><label class="{{ $label }}">Hajiri Designation</label><select name="designation_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['designations'] as $item)<option value="{{ $item->id }}" @selected((int) old('designation_id', $isEdit ? $member->user?->designation_id : ($p?->designation_id ?? 0)) === $item->id)>{{ $item->label }}</option>@endforeach</select></div>
                <div><label class="{{ $label }}">Hajiri Employment Type</label><select name="employment_type_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['employmentTypes'] as $item)<option value="{{ $item->id }}" @selected((int) old('employment_type_id', $isEdit ? $member->user?->employment_type_id : ($p?->employment_type_id ?? 0)) === $item->id)>{{ $item->label }}</option>@endforeach</select></div>
                <div><label class="{{ $label }}">Work Area</label><select name="work_assigned_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['workAssigned'] as $item)<option value="{{ $item->id }}" @selected((int) old('work_assigned_id', $isEdit ? $member->user?->work_assigned_id : ($p?->work_assigned_id ?? 0)) === $item->id)>{{ $item->label }}</option>@endforeach</select></div>
                <div><label class="{{ $label }}">Hajiri Department</label><select name="hajiri_department_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['departments'] as $item)<option value="{{ $item->id }}" @selected((int) old('hajiri_department_id', $isEdit ? $member->user?->hajiri_department_id : ($p?->hajiri_department_id ?? 0)) === $item->id)>{{ $item->label }}</option>@endforeach</select></div>
                <div><label class="{{ $label }}">Bank Name</label><input name="bank_name" value="{{ old('bank_name', $isEdit ? $member->bank_name : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Bank Branch</label><input name="bank_branch" value="{{ old('bank_branch', $isEdit ? $member->bank_branch : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Account Name</label><input name="bank_account_name" value="{{ old('bank_account_name', $isEdit ? $member->bank_account_name : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Account Number</label><input name="bank_account_number" value="{{ old('bank_account_number', $isEdit ? $member->bank_account_number : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">PAN No.</label><input name="pan_number" value="{{ old('pan_number', $isEdit ? $member->pan_number : '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">SSF / CIT</label><div class="grid grid-cols-2 gap-3"><input name="ssf_number" value="{{ old('ssf_number', $isEdit ? $member->ssf_number : '') }}" placeholder="SSF" class="{{ $input }}"><input name="cit_number" value="{{ old('cit_number', $isEdit ? $member->cit_number : '') }}" placeholder="CIT" class="{{ $input }}"></div></div>
            </div>
            </fieldset>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-extrabold text-gray-950">Address</h2>
            <div class="mt-4 space-y-5">

                {{-- Permanent --}}
                <div>
                    <p class="mb-3 text-sm font-extrabold text-gray-700">Permanent Address</p>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <div><label class="{{ $label }}">Province</label><input name="permanent_province" x-model="permProvince" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">District</label><input name="permanent_district" x-model="permDistrict" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">Municipality</label><input name="permanent_municipality" x-model="permMunicipality" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">Ward</label><input name="permanent_ward" x-model="permWard" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">Tole</label><input name="permanent_tole" x-model="permTole" class="{{ $input }}"></div>
                    </div>
                </div>

                {{-- Same as permanent toggle --}}
                <label class="inline-flex cursor-pointer items-center gap-2.5 select-none">
                    <input type="checkbox" x-model="sameAddress" class="h-4 w-4 rounded border-gray-300 text-[#1a5632] focus:ring-[#1a5632]">
                    <span class="text-sm font-bold text-gray-700">Temporary address is same as permanent</span>
                </label>

                {{-- Hidden copies submitted when sameAddress = true --}}
                <template x-if="sameAddress">
                    <div>
                        <input type="hidden" name="temporary_province"     :value="permProvince">
                        <input type="hidden" name="temporary_district"     :value="permDistrict">
                        <input type="hidden" name="temporary_municipality" :value="permMunicipality">
                        <input type="hidden" name="temporary_ward"         :value="permWard">
                        <input type="hidden" name="temporary_tole"         :value="permTole">
                    </div>
                </template>

                {{-- Temporary (only shown when not same) --}}
                <div x-show="!sameAddress" x-transition>
                    <p class="mb-3 text-sm font-extrabold text-gray-700">Temporary Address</p>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <div><label class="{{ $label }}">Province</label><input name="temporary_province" value="{{ old('temporary_province', $isEdit ? $member->temporary_province : '') }}" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">District</label><input name="temporary_district" value="{{ old('temporary_district', $isEdit ? $member->temporary_district : '') }}" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">Municipality</label><input name="temporary_municipality" value="{{ old('temporary_municipality', $isEdit ? $member->temporary_municipality : '') }}" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">Ward</label><input name="temporary_ward" value="{{ old('temporary_ward', $isEdit ? $member->temporary_ward : '') }}" class="{{ $input }}"></div>
                        <div><label class="{{ $label }}">Tole</label><input name="temporary_tole" value="{{ old('temporary_tole', $isEdit ? $member->temporary_tole : '') }}" class="{{ $input }}"></div>
                    </div>
                </div>

            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-extrabold text-gray-950">Login{{ !$isEdit ? ', Library & Transport' : '' }}</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                {{-- Login User ID — students only (employees log in via email) --}}
                <div x-show="isStudent" x-transition>
                    <label class="{{ $label }}">Login User ID</label>
                    <input name="login_user_id"
                           value="{{ old('login_user_id', $isEdit ? $member->user?->student_code : ($p?->student_code ?? '')) }}"
                           placeholder="Blank = roll number"
                           class="{{ $input }}">
                </div>
                <div><label class="{{ $label }}">Login Password</label><input type="password" name="password" placeholder="{{ $isEdit ? 'New password optional' : 'Blank = Login User ID / Email' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">Confirm Password</label><input type="password" name="password_confirmation" class="{{ $input }}"></div>
                <div x-show="isStudent" x-transition><label class="{{ $label }}">Batch</label><input name="batch" value="{{ old('batch', $isEdit ? $member->batch : '') }}" class="{{ $input }}"></div>
                <div x-show="isStudent" x-transition><label class="{{ $label }}">Library ID</label><input name="library_id" value="{{ old('library_id', $isEdit ? $member->library_id : '') }}" class="{{ $input }}"></div>
                <div x-show="isStudent" x-transition><label class="{{ $label }}">Bus Route</label><input name="bus_route" value="{{ old('bus_route', $isEdit ? $member->bus_route : '') }}" class="{{ $input }}"></div>
                <div x-show="isStudent" x-transition><label class="{{ $label }}">Bus Stop</label><input name="bus_stop" value="{{ old('bus_stop', $isEdit ? $member->bus_stop : '') }}" class="{{ $input }}"></div>
            </div>
        </section>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.hr.members.index') }}" class="inline-flex justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-extrabold text-gray-700 hover:bg-gray-50">Cancel</a>
            <button class="rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415]">{{ $isEdit ? 'Update Member' : 'Create Member' }}</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function hrMemberForm() {
        return {
            options: @json($formOptions),
            memberType: @json($selectedType),
            organization: @json($selectedOrganization),
            stream: @json($selectedStream),
            section: @json($selectedSection),
            fatherName: @json(old('father_name', $isEdit ? $member->father_name : '')),
            motherName: @json(old('mother_name', $isEdit ? $member->mother_name : '')),
            parentContact: @json(old('parent_contact', $isEdit ? $member->parent_contact : ($p?->phone ?? ''))),
            guardianRelation: @json(old('guardian_relation', $isEdit ? ($member->guardian_relation ?: 'father') : 'father')),
            customGuardianName: @json(old('guardian_name', $isEdit ? $member->guardian_name : '')),
            guardianContact: @json(old('guardian_contact', $isEdit ? ($member->guardian_contact ?: $member->parent_contact) : ($p?->phone ?? ''))),
            // Address state
            permProvince:     @json(old('permanent_province',     $isEdit ? $member->permanent_province     : ($p?->province  ?? ''))),
            permDistrict:     @json(old('permanent_district',     $isEdit ? $member->permanent_district     : ($p?->district  ?? ''))),
            permMunicipality: @json(old('permanent_municipality', $isEdit ? $member->permanent_municipality : ($p?->municipal ?? ''))),
            permWard:         @json(old('permanent_ward',         $isEdit ? $member->permanent_ward         : '')),
            permTole:         @json(old('permanent_tole',         $isEdit ? $member->permanent_tole         : '')),
            sameAddress: @json(
                old('same_address') !== null
                    ? (bool) old('same_address')
                    : ($isEdit
                        ? ($member->permanent_province === $member->temporary_province
                           && $member->permanent_district === $member->temporary_district
                           && $member->permanent_municipality === $member->temporary_municipality)
                        : false)
            ),
            get isStudent() { return this.memberType === 'student'; },
            get isEmployee() { return this.memberType === 'teacher' || this.memberType === 'staff'; },
            get streams() { return Object.keys(this.options[this.organization]?.streams || {}); },
            get sections() { return this.options[this.organization]?.streams?.[this.stream] || []; },
            get guardianName() {
                if (this.guardianRelation === 'father') return this.fatherName;
                if (this.guardianRelation === 'mother') return this.motherName;
                if (this.guardianRelation === 'guardian') return this.customGuardianName;
                return this.customGuardianName;
            },
            init() {
                this.$watch('memberType', value => {
                    if (value === 'teacher') this.guardianRelation = 'father';
                    if (value === 'staff') this.guardianRelation = 'father';
                });
                this.$watch('organization', () => {
                    if (!this.streams.includes(this.stream)) this.stream = '';
                    this.section = '';
                });
                this.$watch('stream', () => {
                    if (!this.sections.includes(this.section)) this.section = '';
                });
                this.$watch('parentContact', value => {
                    if (!this.guardianContact) this.guardianContact = value;
                });
            }
        }
    }
</script>
@endpush
@endsection
