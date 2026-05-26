@extends('hr.layouts.app')

@section('title', 'Bulk Import HR Members')

@section('content')
@php
    $input = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15';
    $label = 'block text-xs font-extrabold uppercase tracking-widest text-gray-500 mb-1.5';
    $selectedOrganization = old('organization', array_key_first($formOptions));
@endphp

<div class="space-y-6" x-data="hrImportForm()">
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
        <p class="text-sm font-bold uppercase tracking-widest text-white/50">Human Resource</p>
        <h1 class="mt-1 text-3xl font-extrabold">Bulk Import Members</h1>
        <p class="mt-2 max-w-3xl text-sm font-medium text-white/70">
            Import students, academic employees, and administrative employees from CSV. Imported records sync to ID Card, Login, Hajiri, and Learning.
        </p>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1fr_360px]">
        <form method="POST" action="{{ route('admin.hr.members.import.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            @csrf
            <h2 class="text-lg font-extrabold text-gray-950">CSV Import</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div><label class="{{ $label }}">Default Member Type</label><select name="member_type" class="{{ $input }}"><option value="student">Student</option><option value="teacher">Teacher / Academic Employee</option><option value="staff">Staff / Administrative Employee</option></select></div>
                <div><label class="{{ $label }}">Organization</label><select name="organization" x-model="organization" class="{{ $input }}">@foreach($formOptions as $slug => $organization)<option value="{{ $slug }}">{{ $organization['label'] }}</option>@endforeach</select></div>
                <div><label class="{{ $label }}">Class / Department</label><select name="stream" x-model="stream" class="{{ $input }}"><option value="">Use CSV / none</option><template x-for="streamName in streams" :key="streamName"><option :value="streamName" x-text="streamName"></option></template></select></div>
                <div><label class="{{ $label }}">Section</label><select name="section" x-model="section" class="{{ $input }}"><option value="">Use CSV / none</option><template x-for="sectionName in sections" :key="sectionName"><option :value="sectionName" x-text="sectionName"></option></template></select></div>
                <div><label class="{{ $label }}">Employee Category</label><select name="employee_category" class="{{ $input }}"><option value="">Use CSV / none</option><option value="academic">Academic</option><option value="administrative">Administrative</option></select></div>
                <div><label class="{{ $label }}">CSV File</label><input type="file" name="csv_file" required accept=".csv,text/csv,text/plain" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#1a5632] file:px-4 file:py-2 file:text-sm file:font-bold file:text-white"></div>
            </div>

            <div class="mt-6 border-t border-gray-100 pt-5">
                <h3 class="text-base font-extrabold text-gray-950">Default Hajiri Values for Employees</h3>
                <p class="mt-1 text-sm font-medium text-gray-500">These apply to teacher/staff rows. Student rows ignore Hajiri defaults.</p>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div><label class="{{ $label }}">Designation</label><select name="designation_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['designations'] as $item)<option value="{{ $item->id }}">{{ $item->label }}</option>@endforeach</select></div>
                    <div><label class="{{ $label }}">Employment Type</label><select name="employment_type_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['employmentTypes'] as $item)<option value="{{ $item->id }}">{{ $item->label }}</option>@endforeach</select></div>
                    <div><label class="{{ $label }}">Academic / Administrative</label><select name="work_assigned_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['workAssigned'] as $item)<option value="{{ $item->id }}">{{ $item->label }}</option>@endforeach</select></div>
                    <div><label class="{{ $label }}">Hajiri Department</label><select name="hajiri_department_id" class="{{ $input }}"><option value="">Select</option>@foreach($hajiriOptions['departments'] as $item)<option value="{{ $item->id }}">{{ $item->label }}</option>@endforeach</select></div>
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <a href="{{ route('admin.hr.members.index') }}" class="rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-extrabold text-gray-700 hover:bg-gray-50">Cancel</a>
                <button class="rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415]">Import Members</button>
            </div>
        </form>

        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-extrabold text-gray-950">Template</h2>
                <p class="mt-2 text-sm font-medium text-gray-500">Download the CSV template and fill only the columns you need. Required columns are roll_number, first_name, last_name.</p>
                <a href="{{ route('admin.hr.members.template') }}" class="mt-4 inline-flex rounded-xl bg-[#1a5632] px-4 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415]">Download Template</a>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <p class="text-sm font-extrabold text-blue-950">Import Rules</p>
                <p class="mt-2 text-sm font-medium leading-6 text-blue-800">
                    Academic employees should use member_type teacher. Administrative employees should use member_type staff. Device ID in CSV links the account to Hajiri.
                    Use login_user_id when the portal login ID should be different from roll_number or employee ID. Email login also works when the email column is filled.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function hrImportForm() {
        return {
            options: @json($formOptions),
            organization: @json($selectedOrganization),
            stream: '',
            section: '',
            get streams() { return Object.keys(this.options[this.organization]?.streams || {}); },
            get sections() { return this.options[this.organization]?.streams?.[this.stream] || []; },
            init() {
                this.$watch('organization', () => { this.stream = ''; this.section = ''; });
                this.$watch('stream', () => { this.section = ''; });
            }
        }
    }
</script>
@endpush
@endsection
