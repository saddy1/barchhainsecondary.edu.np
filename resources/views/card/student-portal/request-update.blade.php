@extends('card.student-portal.layout')
@section('title', 'Update Request')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Profile Correction</p>
        <h1 class="mt-2 text-2xl font-extrabold text-gray-950">Request Personal Detail Update</h1>
        <p class="mt-1 text-sm font-medium text-gray-500">
            Fill only the fields that need correction. Your request will be reviewed by administration before changes are applied.
        </p>
    </section>

    @if($pending)
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-sm font-extrabold text-amber-950">You already have a pending request</p>
            <p class="mt-2 text-sm font-semibold leading-6 text-amber-800">
                Fields requested: {{ implode(', ', array_keys($pending->requested_changes ?? [])) }}.
                Submitted {{ $pending->created_at->diffForHumans() }}.
            </p>
        </section>
    @endif

    <form method="POST" action="{{ route('student.submit-update') }}" class="rounded-2xl border border-gray-200 bg-white shadow-sm {{ $pending ? 'pointer-events-none opacity-50' : '' }}">
        @csrf
        <div class="grid gap-6 p-5 lg:grid-cols-2">
            <div>
                <h2 class="text-base font-extrabold text-gray-950">Contact Details</h2>
                <div class="mt-4 space-y-4">
                    @foreach([
                        'mobile' => ['Mobile Number', $student->mobile],
                        'email' => ['Email Address', $student->email],
                        'parent_contact' => ['Parent Contact', $student->parent_contact],
                        'guardian_contact' => ['Guardian Contact', $student->guardian_contact],
                    ] as $field => [$label, $current])
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">{{ $label }}</label>
                            <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" value="{{ old($field) }}" placeholder="Current: {{ $current ?: 'not set' }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-base font-extrabold text-gray-950">Permanent Address</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach([
                        'permanent_province' => ['Province', $student->permanent_province],
                        'permanent_district' => ['District', $student->permanent_district],
                        'permanent_municipality' => ['Municipality', $student->permanent_municipality],
                        'permanent_ward' => ['Ward', $student->permanent_ward],
                        'permanent_tole' => ['Tole', $student->permanent_tole],
                    ] as $field => [$label, $current])
                        <div class="{{ $field === 'permanent_tole' ? 'sm:col-span-2' : '' }}">
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">{{ $label }}</label>
                            <input name="{{ $field }}" value="{{ old($field) }}" placeholder="Current: {{ $current ?: 'not set' }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 p-5">
            <h2 class="text-base font-extrabold text-gray-950">Temporary Address</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-5">
                @foreach([
                    'temporary_province' => ['Province', $student->temporary_province],
                    'temporary_district' => ['District', $student->temporary_district],
                    'temporary_municipality' => ['Municipality', $student->temporary_municipality],
                    'temporary_ward' => ['Ward', $student->temporary_ward],
                    'temporary_tole' => ['Tole', $student->temporary_tole],
                ] as $field => [$label, $current])
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-widest text-gray-500">{{ $label }}</label>
                        <input name="{{ $field }}" value="{{ old($field) }}" placeholder="{{ $current ?: 'Current not set' }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50 p-5 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs font-semibold leading-5 text-gray-500">Blank fields are ignored. Only changed values will be sent for admin review.</p>
            <button class="rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415]">Submit Update Request</button>
        </div>
    </form>
</div>
@endsection
