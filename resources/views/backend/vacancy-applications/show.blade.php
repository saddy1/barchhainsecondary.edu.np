@extends('layouts.admin')
@section('title', 'Application — ' . $application->full_name)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.vacancies.applications', $application->vacancy_id) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#1a5632] font-medium mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Applications
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Application Review</h2>
            <p class="text-sm text-gray-500 mt-1">Position: <span class="font-bold text-[#1a5632]">{{ $application->vacancy->title }}</span></p>
        </div>
        {{-- Print / Download Buttons --}}
        <div class="flex gap-3 flex-wrap">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Application
            </button>
            @if($application->cv_path)
            <a href="{{ asset($application->cv_path) }}" target="_blank" download
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1a5632] text-white font-bold text-sm rounded-xl hover:bg-[#0b2415] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download CV
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-sm font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- ── SECTION 1: Profile Photo + Basic Info ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 print-section">
        <div class="flex flex-col sm:flex-row gap-6 mb-6">
            @if($application->profile_photo)
            <div class="shrink-0">
                <img src="{{ asset($application->profile_photo) }}" alt="Profile Photo"
                    class="w-28 h-28 rounded-xl object-cover border-2 border-[#1a5632]/20 shadow">
                <p class="text-xs text-gray-400 text-center mt-1">Profile Photo</p>
            </div>
            @endif
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h3 class="font-bold text-[#0b2415] text-xl">{{ $application->full_name }}</h3>
                    @php
                        $statusColors = ['Pending'=>'bg-yellow-100 text-yellow-800','Reviewed'=>'bg-blue-100 text-blue-800','Shortlisted'=>'bg-green-100 text-green-800','Rejected'=>'bg-red-100 text-red-800'];
                    @endphp
                    <span class="text-xs font-bold px-3 py-1 rounded-full {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $application->status }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">Applied {{ $application->created_at->diffForHumans() }} &middot; {{ $application->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
        </div>

        <h4 class="font-bold text-[#0b2415] text-base mb-4 pb-3 border-b border-gray-100">Contact Information</h4>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Email</p>
                <a href="mailto:{{ $application->email }}" class="text-[#1a5632] font-bold hover:underline text-sm">{{ $application->email }}</a>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Phone</p>
                <a href="tel:{{ $application->phone }}" class="text-gray-900 font-bold hover:text-[#1a5632] text-sm">{{ $application->phone }}</a>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Current Address</p>
                <p class="text-gray-700 text-sm">{{ $application->address ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ── SECTION 2: Personal Details ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 print-section">
        <h4 class="font-bold text-[#0b2415] text-base mb-4 pb-3 border-b border-gray-100">Personal Details</h4>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Date of Birth</p>
                <p class="text-gray-900 font-bold text-sm">{{ $application->date_of_birth ? \Carbon\Carbon::parse($application->date_of_birth)->format('M d, Y') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Gender</p>
                <p class="text-gray-900 font-bold text-sm">{{ $application->gender ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Father's Name</p>
                <p class="text-gray-900 font-bold text-sm">{{ $application->father_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Mother's Name</p>
                <p class="text-gray-900 font-bold text-sm">{{ $application->mother_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Permanent Address</p>
                <p class="text-gray-700 text-sm">{{ $application->permanent_address ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Temporary Address</p>
                <p class="text-gray-700 text-sm">{{ $application->temporary_address ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ── SECTION 3: Citizenship ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 print-section">
        <h4 class="font-bold text-[#0b2415] text-base mb-4 pb-3 border-b border-gray-100">Citizenship Documents</h4>
        <div class="mb-4">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Citizenship Number</p>
            <p class="text-gray-900 font-bold text-sm">{{ $application->citizenship_no ?? '—' }}</p>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            @if($application->citizen_front_path)
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Citizenship — Front</p>
                @if(Str::endsWith($application->citizen_front_path, ['.jpg','.jpeg','.png']))
                <img src="{{ asset($application->citizen_front_path) }}" alt="Citizenship Front"
                    class="w-full max-w-xs rounded-xl border border-gray-200 shadow-sm mb-2">
                @endif
                <div class="flex gap-2">
                    <a href="{{ asset($application->citizen_front_path) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-xs font-bold text-[#1a5632] hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        View
                    </a>
                    <a href="{{ asset($application->citizen_front_path) }}" download
                        class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 hover:text-[#1a5632]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                        Download
                    </a>
                </div>
            </div>
            @endif
            @if($application->citizen_back_path)
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Citizenship — Back</p>
                @if(Str::endsWith($application->citizen_back_path, ['.jpg','.jpeg','.png']))
                <img src="{{ asset($application->citizen_back_path) }}" alt="Citizenship Back"
                    class="w-full max-w-xs rounded-xl border border-gray-200 shadow-sm mb-2">
                @endif
                <div class="flex gap-2">
                    <a href="{{ asset($application->citizen_back_path) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-xs font-bold text-[#1a5632] hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        View
                    </a>
                    <a href="{{ asset($application->citizen_back_path) }}" download
                        class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 hover:text-[#1a5632]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                        Download
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── SECTION 4: Signature ── --}}
    @if($application->signature_path)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 print-section">
        <h4 class="font-bold text-[#0b2415] text-base mb-4 pb-3 border-b border-gray-100">Signature</h4>
        @if(Str::endsWith($application->signature_path, ['.jpg','.jpeg','.png']))
        <img src="{{ asset($application->signature_path) }}" alt="Signature"
            class="h-20 rounded-lg border border-gray-200 shadow-sm mb-2">
        @endif
        <div class="flex gap-2 mt-2">
            <a href="{{ asset($application->signature_path) }}" target="_blank"
                class="inline-flex items-center gap-1 text-xs font-bold text-[#1a5632] hover:underline">View</a>
            <a href="{{ asset($application->signature_path) }}" download
                class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 hover:text-[#1a5632]">Download</a>
        </div>
    </div>
    @endif

    {{-- ── SECTION 5: Qualifications ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 print-section">
        <h4 class="font-bold text-[#0b2415] text-base mb-4 pb-3 border-b border-gray-100">Qualifications & Experience</h4>
        <div class="grid sm:grid-cols-2 gap-5 mb-6">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Highest Qualification</p>
                <p class="text-gray-900 font-bold text-sm">{{ $application->qualification }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Experience</p>
                <p class="text-gray-700 text-sm">{{ $application->experience ?? '—' }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Motivation / Cover Letter</p>
            <div class="bg-[#fdfbf7] rounded-xl p-5 border border-gray-100 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $application->motivation }}</div>
        </div>

        <div class="flex flex-wrap gap-3">
            @if($application->cv_path)
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">CV / Resume</p>
                <div class="flex gap-2">
                    <a href="{{ asset($application->cv_path) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1a5632] text-white font-bold text-sm rounded-xl hover:bg-[#0b2415] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        View CV
                    </a>
                    <a href="{{ asset($application->cv_path) }}" download
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download CV
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Status Update ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 no-print">
        <h4 class="font-bold text-[#0b2415] text-base mb-6 pb-4 border-b border-gray-100">Update Application Status</h4>

        <form action="{{ route('admin.vacancy-applications.update', $application->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                <div class="relative max-w-xs">
                    <select name="status" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] transition-all appearance-none">
                        @foreach(['Pending', 'Reviewed', 'Shortlisted', 'Rejected'] as $s)
                        <option value="{{ $s }}" {{ $application->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Admin Remarks (internal notes)</label>
                <textarea name="admin_remarks" rows="3" placeholder="Private notes about this applicant..."
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] transition-all resize-y">{{ $application->admin_remarks }}</textarea>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="px-8 py-3 bg-[#1a5632] text-white font-bold rounded-xl hover:bg-[#0b2415] transition-colors shadow-sm">
                    Save Status
                </button>
                <a href="{{ route('admin.vacancies.applications', $application->vacancy_id) }}" class="px-8 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .print-section { box-shadow: none !important; border: 1px solid #e5e7eb !important; break-inside: avoid; }
}
</style>
@endsection
