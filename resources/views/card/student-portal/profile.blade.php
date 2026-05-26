@extends('card.student-portal.layout')
@section('title', $student->profile_completed_at ? 'My Profile' : 'Complete Profile')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Personal Information</p>
                <h1 class="mt-2 text-2xl font-extrabold text-gray-950">{{ $student->profile_completed_at ? 'My Profile' : 'Complete Your Profile' }}</h1>
                <p class="mt-1 text-sm font-medium text-gray-500">
                    {{ $student->profile_completed_at ? 'Review your approved details. Use Update Request for corrections.' : 'Complete required contact details before using portal services.' }}
                </p>
            </div>
            @if($student->profile_completed_at)
                <a href="{{ route('student.request-update') }}" class="inline-flex justify-center rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-extrabold text-gray-700 hover:bg-gray-50">Request Correction</a>
            @endif
        </div>
    </section>

    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="grid gap-5 xl:grid-cols-[320px_1fr]">
        @csrf

        <aside class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mx-auto h-56 w-44 overflow-hidden rounded-2xl border border-gray-200 bg-gray-100">
                @if($student->photo)
                    <img src="{{ $student->photo_url }}" alt="" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-4xl font-extrabold text-gray-300">{{ strtoupper(substr($student->first_name, 0, 1)) }}</div>
                @endif
            </div>
            <div class="mt-5 text-center">
                <p class="text-base font-extrabold text-gray-950">{{ $student->full_name }}</p>
                <p class="mt-1 text-sm font-semibold text-gray-400">{{ $student->roll_number }} · {{ $student->stream ?: 'Class not set' }}</p>
            </div>
            <div class="mt-5">
                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">Profile Photo</label>
                <input type="file" name="photo" accept="image/*" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#1a5632] file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                <p class="mt-2 text-xs font-medium text-gray-400">Passport-size photo, max 200KB.</p>
            </div>
        </aside>

        <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 p-5">
                <h2 class="text-lg font-extrabold text-gray-950">Approved Academic Details</h2>
                <p class="mt-1 text-sm font-medium text-gray-500">These fields are controlled by the school office.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-4">
                @foreach([
                    'First Name' => $student->first_name,
                    'Last Name' => $student->last_name,
                    'Roll / ID' => $student->roll_number,
                    'Class' => $student->stream,
                    'Section' => $student->section,
                    'Registration No.' => $student->registration_no,
                ] as $label => $value)
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">{{ $label }}</label>
                        <input value="{{ $value }}" disabled class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-500">
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 p-5">
                <h2 class="text-lg font-extrabold text-gray-950">Required Contact Details</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob', $student->dob?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">Mobile Number</label>
                        <input name="mobile" value="{{ old('mobile', $student->mobile) }}" required class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">Email</label>
                        <input type="email" name="email" value="{{ old('email', $student->email) }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">Citizenship No.</label>
                        <input name="citizenship_no" value="{{ old('citizenship_no', $student->citizenship_no) }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">Batch</label>
                        <input name="batch" value="{{ old('batch', $student->batch) }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50 p-5 text-right">
                <button class="rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415]">{{ $student->profile_completed_at ? 'Save Profile' : 'Save and Continue' }}</button>
            </div>
        </section>
    </form>
</div>
@endsection
