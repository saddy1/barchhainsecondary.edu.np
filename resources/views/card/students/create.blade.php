@extends('card.layouts.app')
@section('title', 'Add Member')
@section('heading', isset($student) ? 'Edit Member' : 'Add New Member')

@section('content')
@php
    $isEdit = isset($student);
    $selectedOrganization = old('organization', $isEdit ? $student->organization : 'college');
    $selectedStream = old('stream', $isEdit ? $student->stream : '');
    $selectedSection = old('section', $isEdit ? $student->section : '');
@endphp

<div class="max-w-6xl">
<form method="POST"
      action="{{ $isEdit ? route('students.update', $student) : route('students.store') }}"
      enctype="multipart/form-data"
      class="space-y-4">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Member Type --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-primary mb-3 text-sm uppercase tracking-wide">Member Type</h2>
        <div class="grid grid-cols-3 gap-3">
            @foreach(['student' => '🎓 Student', 'teacher' => '👨‍🏫 Teacher', 'staff' => '👷 Staff'] as $val => $label)
            <label class="relative cursor-pointer">
                <input type="radio" name="member_type" value="{{ $val }}"
                       class="peer sr-only" {{ old('member_type', $isEdit ? $student->member_type : '') == $val ? 'checked' : '' }}>
                <div class="border-2 border-gray-200 rounded-lg p-3 text-center text-sm font-medium
                            peer-checked:border-primary peer-checked:bg-blue-50 peer-checked:text-primary
                            hover:border-gray-300 transition">
                    {{ $label }}
                </div>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Basic Info --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-primary mb-3 text-sm uppercase tracking-wide">Basic Information</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div>
    <label class="block text-xs font-medium text-gray-600 mb-1">Organization <span class="text-red-500">*</span></label>
    <select name="organization" id="organizationSelect" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
        @foreach($formOptions as $slug => $organization)
            <option value="{{ $slug }}" {{ $selectedOrganization === $slug ? 'selected' : '' }}>{{ $organization['label'] }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-xs font-medium text-gray-600 mb-1">Department / Class</label>
    <select name="stream" id="streamSelect"
           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
    </select>
</div>
<div>
    <label class="block text-xs font-medium text-gray-600 mb-1">Section</label>
    <select name="section" id="sectionSelect"
           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
    </select>
</div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Roll / ID Number <span class="text-red-500">*</span></label>
                <input type="text" name="roll_number" value="{{ old('roll_number', $isEdit ? $student->roll_number : '') }}"
                       placeholder="e.g. PUR071BEL019 or ST-01"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Mobile <span class="text-red-500"></span></label>
                <input type="text" name="mobile" value="{{ old('mobile', $isEdit ? $student->mobile : '') }}"
                       placeholder="98XXXXXXXX"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name', $isEdit ? $student->first_name : '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Middle Name</label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $isEdit ? $student->middle_name : '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Last Name <span class="text-red-500">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name', $isEdit ? $student->last_name : '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div id="guardianNameField">
                <label class="block text-xs font-medium text-gray-600 mb-1">Guardian Name</label>
                <input type="text" name="guardian_name" value="{{ old('guardian_name', $isEdit ? $student->guardian_name : '') }}"
                       placeholder="Father / Mother / Guardian"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div id="registrationNoField">
                <label class="block text-xs font-medium text-gray-600 mb-1">Registration No.</label>
                <input type="text" name="registration_no" value="{{ old('registration_no', $isEdit ? $student->registration_no : '') }}"
                       placeholder="e.g. REG-2080-001"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date of Birth <span class="text-red-500"></span></label>
                <input type="date" name="dob" value="{{ old('dob', $isEdit ? $student->dob?->format('Y-m-d') : '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $isEdit ? $student->email : '') }}"
                       placeholder="name@ioepc.edu.np"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Valid Till <span class="text-red-500">*</span> <span class="text-gray-400">(required for students)</span></label>
                <input type="date" name="valid_till" value="{{ old('valid_till', $isEdit ? $student->valid_till?->format('Y-m-d') : '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Citizenship No.</label>
                <input type="text" name="citizenship_no" value="{{ old('citizenship_no', $isEdit ? $student->citizenship_no : '') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
        </div>
    </div>

    {{-- Photo --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-primary mb-4 text-sm uppercase tracking-wide">Photo</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Photo Preview --}}
            <div class="flex flex-col items-center">
                <div class="relative w-48 h-60 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden mb-4">
                    @if($isEdit && $student->photo)
                        <img src="{{ $student->photo_url }}" class="w-full h-full object-cover" id="photo-preview">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-400" id="photo-placeholder">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="text-sm text-center px-2">No photo selected</p>
                        </div>
                        <img id="photo-preview" class="w-full h-full object-cover hidden">
                    @endif

                    {{-- Camera Feed Overlay --}}
                    <video id="camera-feed" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>

                    {{-- Loading/Capturing Indicator --}}
                    <div id="camera-loading" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                        <div class="text-white text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                            <p class="text-sm">Starting camera...</p>
                        </div>
                    </div>
                </div>

                {{-- Photo Status --}}
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1" id="photo-status">Passport size photo required</p>
                    <p class="text-xs text-gray-400">Student: max 200 KB, Staff/Teacher: max 2 MB</p>
                    <p class="text-xs text-gray-400">Recommended: 150×200px</p>
                </div>
            </div>

            {{-- Photo Controls --}}
            <div class="flex flex-col justify-center space-y-4">
                {{-- Camera Capture Section --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-medium text-green-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Capture with Camera
                    </h3>

                    {{-- Camera Selection --}}
                    <div id="camera-selection" class="mb-3 hidden">
                        <label class="block text-xs font-medium text-green-700 mb-1">Select Camera:</label>
                        <select id="camera-select" class="w-full border border-green-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                            <option value="">Loading cameras...</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <button type="button" id="start-camera"
                                class="w-full bg-green-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-green-700 transition flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Open Camera
                        </button>
                        <button type="button" id="capture-photo"
                                class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition hidden flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Capture Photo
                        </button>
                        <button type="button" id="retake-photo"
                                class="w-full bg-gray-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition hidden flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Change Photo
                        </button>
                    </div>
                </div>

                {{-- File Upload Section --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-medium text-blue-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload from Device
                    </h3>
                    <div class="space-y-2">
                        <label for="upload-photo" class="cursor-pointer">
                            <div class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Choose Photo File
                            </div>
                            <input type="file" accept="image/*" id="upload-photo" class="hidden">
                        </label>
                        <p class="text-xs text-blue-600 text-center" id="upload-status">Click to browse and select image file</p>
                    </div>
                </div>

                {{-- Hidden Elements --}}
                <canvas id="photo-canvas" class="hidden"></canvas>
                <input type="file" name="photo" accept="image/*" id="photo-input" class="hidden">
            </div>
        </div>
    </div>

    {{-- Staff/Teacher Only Fields --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5" id="staffFields">
        <h2 class="font-semibold text-primary mb-3 text-sm uppercase tracking-wide">Employment Details <span class="text-xs text-gray-400 font-normal">(for Staff/Teacher only)</span></h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Designation</label>
                <input type="text" name="designation" value="{{ old('designation', $isEdit ? $student->designation : '') }}"
                       placeholder="e.g. Assistant Professor"
                       list="list-designation"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <datalist id="list-designation"></datalist>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Employment Type</label>
                <input type="text" name="employment_type" value="{{ old('employment_type', $isEdit ? $student->employment_type : '') }}"
                       placeholder="e.g. Permanent / Contract"
                       list="list-employment_type"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <datalist id="list-employment_type"></datalist>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Program / Department</label>
                <input type="text" name="program" value="{{ old('program', $isEdit ? $student->program : '') }}"
                       placeholder="e.g. BE Computer / Civil Dept."
                       list="list-program"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <datalist id="list-program"></datalist>
            </div>
        </div>
    </div>

    {{-- Cards to Issue --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-primary mb-3 text-sm uppercase tracking-wide">Cards to Issue</h2>
        <div class="space-y-4">
            {{-- Library Card --}}
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="has_library_card" value="1"
                       {{ old('has_library_card', $isEdit ? $student->has_library_card : false) ? 'checked' : '' }}
                       class="mt-1 accent-primary" id="libCheck">
                <div>
                    <p class="font-medium text-sm">📚 Library Card</p>
                    <p class="text-xs text-gray-400">Generates a library membership card with auto-assigned Library ID</p>
                </div>
            </label>
            <div id="libIdField" class="ml-6 {{ old('has_library_card', $isEdit ? $student->has_library_card : false) ? '' : 'hidden' }}">
                <label class="block text-xs font-medium text-gray-600 mb-1">Library ID (auto-generated if empty)</label>
                <input type="text" name="library_id" value="{{ old('library_id', $isEdit ? $student->library_id : '') }}"
                       placeholder="e.g. LIB-STU-0001"
                       class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-64">
            </div>

            {{-- Bus Pass --}}
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="has_bus_pass" value="1"
                       {{ old('has_bus_pass', $isEdit ? $student->has_bus_pass : false) ? 'checked' : '' }}
                       class="mt-1 accent-primary" id="busCheck">
                <div>
                    <p class="font-medium text-sm">🚌 Bus Pass</p>
                    <p class="text-xs text-gray-400">Generates a campus bus pass with route information</p>
                </div>
            </label>
            <div id="busFields" class="ml-6 grid grid-cols-2 gap-3 {{ old('has_bus_pass', $isEdit ? $student->has_bus_pass : false) ? '' : 'hidden' }}">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bus Route</label>
                    <input type="text" name="bus_route" value="{{ old('bus_route', $isEdit ? $student->bus_route : '') }}"
                           placeholder="e.g. Route 3 — Biratnagar"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bus Stop</label>
                    <input type="text" name="bus_stop" value="{{ old('bus_stop', $isEdit ? $student->bus_stop : '') }}"
                           placeholder="e.g. Barchhain Bazaar"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
        </div>
    </div>

    {{-- Portal Login --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5" id="learningLoginPanel">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-primary mb-1 text-sm uppercase tracking-wide">Portal Login</h2>
                <p class="text-sm text-gray-500">Use this same member record for ID Card, Library and E-Learning access.</p>
                @if($isEdit && $student->user_id)
                    <p class="mt-2 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        Login linked: {{ $student->user?->student_code ?? $student->roll_number }}
                    </p>
                @endif
            </div>
            <label class="flex items-start gap-3 cursor-pointer rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <input type="checkbox" name="create_learning_account" value="1"
                       {{ old('create_learning_account', $isEdit ? (bool) $student->user_id : true) ? 'checked' : '' }}
                       class="mt-1 accent-primary" id="learningLoginCheck">
                <span>
                    <span class="block text-sm font-semibold text-gray-800">Enable portal login</span>
                    <span class="block text-xs text-gray-500">User ID will be the Roll / ID Number.</span>
                </span>
            </label>
        </div>

        <div id="learningPasswordFields" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Password</label>
                <input type="password" name="learning_password"
                       placeholder="{{ $isEdit && $student->user_id ? 'Leave blank to keep current password' : 'Leave blank to use Roll / ID Number' }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Confirm Password</label>
                <input type="password" name="learning_password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-primary">
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex gap-3">
        <button type="submit"
                class="bg-primary text-white px-8 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-light transition">
            {{ $isEdit ? 'Update Member' : 'Add Member & Generate Cards' }}
        </button>
        <a href="{{ route('students.index') }}"
           class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">
            Cancel
        </a>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
    const formOptions = @json($formOptions);
    const selectedStream = @json($selectedStream);
    const selectedSection = @json($selectedSection);
    const organizationSelect = document.getElementById('organizationSelect');
    const streamSelect = document.getElementById('streamSelect');
    const sectionSelect = document.getElementById('sectionSelect');

    function fillSelect(select, options, selectedValue, placeholder) {
        select.innerHTML = '';

        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder;
        select.appendChild(placeholderOption);

        options.forEach(function(optionValue) {
            const option = document.createElement('option');
            option.value = optionValue;
            option.textContent = optionValue;
            option.selected = optionValue === selectedValue;
            select.appendChild(option);
        });
    }

    function refreshSections(selectedValue = '') {
        const organization = formOptions[organizationSelect.value] || {streams: {}};
        const sections = organization.streams[streamSelect.value] || [];

        fillSelect(sectionSelect, sections, selectedValue, sections.length ? '-- Select Section --' : '-- No sections available --');
        sectionSelect.disabled = sections.length === 0;
    }

    function refreshStreams(selectedStreamValue = '', selectedSectionValue = '') {
        const organization = formOptions[organizationSelect.value] || {streams: {}};
        const streams = Object.keys(organization.streams || {}).sort();

        fillSelect(streamSelect, streams, selectedStreamValue, streams.length ? '-- Select Department / Class --' : '-- No departments/classes available --');
        streamSelect.disabled = streams.length === 0;

        refreshSections(selectedSectionValue);
    }

    organizationSelect?.addEventListener('change', function() {
        refreshStreams('', '');
    });

    streamSelect?.addEventListener('change', function() {
        refreshSections('');
    });

    refreshStreams(selectedStream, selectedSection);

    // Show guardian/registration fields only for school org
    function toggleSchoolFields() {
        const isSchool = organizationSelect.value === 'school';
        document.getElementById('guardianNameField').style.display   = isSchool ? '' : 'none';
        document.getElementById('registrationNoField').style.display = isSchool ? '' : 'none';
    }
    organizationSelect?.addEventListener('change', toggleSchoolFields);
    toggleSchoolFields();

    function toggleLearningLoginPanel() {
        const selectedType = document.querySelector('input[name="member_type"]:checked')?.value;
        const isStudent = selectedType === 'student' || selectedType === 'teacher';
        const panel = document.getElementById('learningLoginPanel');
        const check = document.getElementById('learningLoginCheck');
        const fields = document.getElementById('learningPasswordFields');

        panel.classList.toggle('hidden', !isStudent);
        fields.classList.toggle('hidden', !check.checked);
    }

    document.querySelectorAll('input[name="member_type"]').forEach(function(input) {
        input.addEventListener('change', toggleLearningLoginPanel);
    });
    document.getElementById('learningLoginCheck')?.addEventListener('change', toggleLearningLoginPanel);
    toggleLearningLoginPanel();

    document.getElementById('libCheck').addEventListener('change', function() {
        document.getElementById('libIdField').classList.toggle('hidden', !this.checked);
    });
    document.getElementById('busCheck').addEventListener('change', function() {
        document.getElementById('busFields').classList.toggle('hidden', !this.checked);
    });

    // ── Autocomplete via datalist ─────────────────────────────────────────
    const suggestFields = ['designation', 'employment_type', 'program'];

    suggestFields.forEach(function(field) {
        const datalist = document.getElementById('list-' + field);
        if (!datalist) return;

        fetch('/api/suggestions?field=' + field)
            .then(r => r.json())
            .then(values => {
                values.forEach(function(v) {
                    const opt = document.createElement('option');
                    opt.value = v;
                    datalist.appendChild(opt);
                });
            });
    });

    // ── Webcam Photo Capture ──────────────────────────────────────────────
    let stream = null;
    let availableCameras = [];
    const video = document.getElementById('camera-feed');
    const canvas = document.getElementById('photo-canvas');
    const photoPreview = document.getElementById('photo-preview');
    const photoPlaceholder = document.getElementById('photo-placeholder');
    const photoStatus = document.getElementById('photo-status');
    const photoInput = document.getElementById('photo-input');
    const uploadPhoto = document.getElementById('upload-photo');
    const uploadStatus = document.getElementById('upload-status');
    const startCameraBtn = document.getElementById('start-camera');
    const captureBtn = document.getElementById('capture-photo');
    const retakeBtn = document.getElementById('retake-photo');
    const cameraSelection = document.getElementById('camera-selection');
    const cameraSelect = document.getElementById('camera-select');
    const cameraLoading = document.getElementById('camera-loading');

    // Function to enumerate available cameras
    async function enumerateCameras() {
        try {
            // Request permission first with timeout
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Camera permission timeout')), 5000);
            });

            await Promise.race([
                navigator.mediaDevices.getUserMedia({ video: true }),
                timeoutPromise
            ]);

            const devices = await navigator.mediaDevices.enumerateDevices();
            availableCameras = devices.filter(device => device.kind === 'videoinput');

            if (availableCameras.length > 1) {
                // Show camera selection if multiple cameras available
                cameraSelect.innerHTML = '';
                availableCameras.forEach((camera, index) => {
                    const option = document.createElement('option');
                    option.value = camera.deviceId;
                    option.textContent = camera.label || `Camera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });
                cameraSelection.classList.remove('hidden');
            } else {
                cameraSelection.classList.add('hidden');
            }

            return availableCameras.length > 0;
        } catch (err) {
            console.error('Error enumerating cameras:', err);
            availableCameras = [];
            cameraSelection.classList.add('hidden');
            return false;
        }
    }

    startCameraBtn.addEventListener('click', async () => {
        try {
            cameraLoading.classList.remove('hidden');
            startCameraBtn.disabled = true;
            startCameraBtn.textContent = 'Accessing Camera...';

            // Enumerate cameras if not already done
            if (availableCameras.length === 0) {
                const hasCameras = await enumerateCameras();
                if (!hasCameras) {
                    throw new Error('No cameras available');
                }
            }

            // Get selected camera or use default
            const selectedDeviceId = cameraSelect.value || (availableCameras.length > 0 ? availableCameras[0].deviceId : undefined);

            const constraints = {
                video: {
                    deviceId: selectedDeviceId ? { exact: selectedDeviceId } : undefined,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            // Add timeout to prevent hanging
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Camera access timeout')), 10000); // 10 second timeout
            });

            stream = await Promise.race([
                navigator.mediaDevices.getUserMedia(constraints),
                timeoutPromise
            ]);
            video.srcObject = stream;
            video.classList.remove('hidden');
            photoPlaceholder.classList.add('hidden');
            photoPreview.classList.add('hidden');

            cameraLoading.classList.add('hidden');
            startCameraBtn.classList.add('hidden');
            captureBtn.classList.remove('hidden');
            photoStatus.textContent = 'Camera active - Click "Capture Photo" when ready';

        } catch (err) {
            cameraLoading.classList.add('hidden');
            startCameraBtn.disabled = false;
            startCameraBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Open Camera
            `;

            // Reset camera state on error
            availableCameras = [];
            cameraSelection.classList.add('hidden');
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            video.classList.add('hidden');

            alert('Unable to access camera. Please check permissions or use file upload.');
            console.error('Camera access error:', err);
        }
    });

    // Handle camera selection change
    cameraSelect.addEventListener('change', () => {
        if (stream) {
            // Stop current stream and restart with new camera
            stream.getTracks().forEach(track => track.stop());
            stream = null;
            video.classList.add('hidden');
            captureBtn.classList.add('hidden');
            startCameraBtn.classList.remove('hidden');
            photoPlaceholder.classList.remove('hidden');
            photoStatus.textContent = 'Camera changed - Click "Open Camera" to continue';
        }
    });

    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        // Stop camera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.classList.add('hidden');

        // Show captured image
        photoPreview.src = canvas.toDataURL('image/jpeg');
        photoPreview.classList.remove('hidden');
        photoPlaceholder.classList.add('hidden');

        // Convert to file
        canvas.toBlob((blob) => {
            const file = new File([blob], 'captured-photo.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            photoInput.files = dt.files;
        }, 'image/jpeg');

        captureBtn.classList.add('hidden');
        retakeBtn.classList.remove('hidden');
        photoStatus.textContent = 'Photo captured successfully';
    });

    retakeBtn.addEventListener('click', () => {
        // Reset all photo-related state
        photoPreview.classList.add('hidden');
        photoPlaceholder.classList.remove('hidden');
        retakeBtn.classList.add('hidden');
        startCameraBtn.classList.remove('hidden');
        captureBtn.classList.add('hidden');
        cameraLoading.classList.add('hidden');
        cameraSelection.classList.add('hidden');

        // Reset camera state
        availableCameras = [];
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.classList.add('hidden');

        // Reset form inputs
        photoInput.value = '';
        uploadPhoto.value = '';

        // Reset status messages
        photoStatus.textContent = 'Passport size photo required';
        uploadStatus.textContent = 'Click to browse and select image file';

        // Reset button states
        startCameraBtn.disabled = false;
        startCameraBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Open Camera
        `;
    });

    uploadPhoto.addEventListener('change', () => {
        if (uploadPhoto.files.length > 0) {
            const file = uploadPhoto.files[0];

            // Create a preview of the uploaded image
            const reader = new FileReader();
            reader.onload = (e) => {
                photoPreview.src = e.target.result;
                photoPreview.classList.remove('hidden');
                photoPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);

            // Clear any camera-related UI
            retakeBtn.classList.remove('hidden');
            startCameraBtn.classList.add('hidden');
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            video.classList.add('hidden');

            // Set the file to the main input
            photoInput.files = uploadPhoto.files;
            photoStatus.textContent = 'Photo uploaded from device';
            uploadStatus.textContent = `Selected: ${file.name}`;
        } else {
            uploadStatus.textContent = 'Click to browse and select image file';
        }
    });
</script>
@endpush
