@extends('card.layouts.app')
@section('title', 'All Members')
@section('heading', 'All Members')

@section('content')
@php
    $selectedOrganization = request('organization', array_key_first($filterOptions ?? []) ?: auth()->user()->organizationSlug());
    $selectedStream = request('stream', '');
    $selectedSection = request('section', '');
@endphp
<div class="space-y-5">

    {{-- ── Toolbar ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col xl:flex-row gap-3 items-stretch xl:items-center justify-between">

        <form method="GET" class="flex gap-2 flex-wrap items-end w-full xl:w-auto">
            <div class="relative w-full sm:w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name, roll number…"
                       class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm w-full
                              focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent">
            </div>

            <select name="type"
                    class="w-full sm:w-auto border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent">
                <option value="">All Types</option>
                <option value="student" @selected(request('type') == 'student')>Students</option>
                <option value="teacher" @selected(request('type') == 'teacher')>Teachers</option>
                <option value="staff"   @selected(request('type') == 'staff')>Staff</option>
            </select>

            @if(auth()->user()->isSuperAdmin())
                <select name="organization" id="filterOrganization"
                        class="w-full sm:w-auto border border-gray-200 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent">
                    @foreach($filterOptions as $slug => $organization)
                        <option value="{{ $slug }}" @selected($selectedOrganization === $slug)>{{ $organization['label'] }}</option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="organization" value="{{ $selectedOrganization }}">
            @endif

            <select name="stream" id="filterStream"
                    class="w-full sm:w-auto border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent"></select>

            <select name="section" id="filterSection"
                    class="w-full sm:w-auto border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent"></select>

            {{-- Per-page selector: auto-submits so page resets to 1 --}}
            <select name="per_page" onchange="this.form.submit()"
                    class="w-full sm:w-auto border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent">
                <option value="10"  @selected(request('per_page') === '10')>10 / page</option>
                <option value="20"  @selected(request('per_page', '20') === '20')>20 / page</option>
                <option value="40"  @selected(request('per_page') === '40')>40 / page</option>
                <option value="100" @selected(request('per_page') === '100')>100 / page</option>
                <option value="all" @selected(request('per_page') === 'all')>All</option>
            </select>

            <button type="submit"
                    class="w-full sm:w-auto bg-primary text-white px-4 py-2 rounded-lg text-sm
                           hover:bg-primary-light transition font-medium">
                Search
            </button>

            @if(request('search') || request('type') || request('stream') || request('section') || request('per_page'))
            <a href="{{ route('students.index') }}"
               class="w-full sm:w-auto text-center px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 transition">
                Clear
            </a>
            @endif
        </form>

        <div class="flex gap-2 flex-shrink-0 flex-wrap w-full xl:w-auto">
            <a href="{{ route('admin.hr.members.import') }}"
               class="flex flex-1 sm:flex-none items-center justify-center gap-2 bg-emerald-600 text-white font-semibold
                      px-4 py-2 rounded-lg text-sm hover:bg-emerald-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import
            </a>
            <a href="{{ route('bulk.index') }}"
               class="flex flex-1 sm:flex-none items-center justify-center gap-2 bg-accent text-primary-dark font-semibold
                      px-4 py-2 rounded-lg text-sm hover:bg-accent-light transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2
                             m2 4h6a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2z"/>
                </svg>
                Bulk Print
            </a>
            <a href="{{ \App\Services\ModuleService::enabled('hr') && auth()->user()->canAccess('hr.members.create') ? route('admin.hr.members.create') : route('students.create') }}"
               class="flex flex-1 sm:flex-none items-center justify-center gap-2 bg-primary text-white px-4 py-2 rounded-lg
                      text-sm hover:bg-primary-light transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add from HR
            </a>
        </div>
    </div>

    {{-- ── Stats strip ────────────────────────────────────────────── --}}
    @php
        $total    = $students->total();
        $typeMap  = ['student' => ['label' => 'Students', 'color' => 'blue'],
                     'teacher' => ['label' => 'Teachers', 'color' => 'purple'],
                     'staff'   => ['label' => 'Staff',    'color' => 'green']];
    @endphp
    <div class="flex gap-3 flex-wrap text-sm">
        <span class="w-full sm:w-auto bg-white border border-gray-100 rounded-lg px-4 py-2 text-gray-600 shadow-sm">
            <span class="font-bold text-gray-900">{{ $total }}</span> total members
        </span>
        @if(($expiredCount ?? 0) > 0)
        <span class="w-full sm:w-auto bg-red-50 border border-red-100 rounded-lg px-4 py-2 text-red-700 shadow-sm text-xs flex items-center gap-1">
            {{ $expiredCount }} expired member{{ $expiredCount > 1 ? 's' : '' }} need attention
        </span>
        @endif
        @if(request('search') || request('type') || request('stream') || request('section'))
        <span class="w-full sm:w-auto bg-blue-50 border border-blue-100 rounded-lg px-4 py-2 text-blue-700 shadow-sm text-xs flex items-center gap-1">
            Filtered results — showing {{ $students->count() }} of {{ $total }}
        </span>
        @endif
    </div>

    @if(($expiredCount ?? 0) > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-700">
        Expired members were found in the current list. Renew their `Valid Till` date before printing or issuing updated cards.
    </div>
    @endif

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-4 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- ── Bulk valid_till panel ──────────────────────────────────────── --}}
    <div id="bulkBar" class="hidden bg-blue-50 border border-blue-200 rounded-xl px-4 sm:px-5 py-3 flex flex-wrap items-center gap-3">
        <span class="text-sm font-medium text-blue-700" id="selectedCount">0 selected</span>
        <span class="text-blue-300">|</span>
        <label class="text-sm text-blue-700 font-medium">Set Valid Till:</label>
        <input type="date" name="valid_till" form="bulkForm"
               class="border border-blue-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
        <button type="submit" form="bulkForm"
                class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            Update Selected
        </button>
        <span class="text-blue-300">|</span>
        <label class="text-sm text-blue-700 font-medium">Learning Password:</label>
        <input type="password" name="learning_password" form="bulkLearningForm"
               class="border border-blue-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="Min 8 chars">
        <input type="password" name="learning_password_confirmation" form="bulkLearningForm"
               class="border border-blue-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="Confirm">
        <button type="button" onclick="submitBulkLearningAccounts()"
                class="bg-primary text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-primary-light transition">
            Enable Learning Login
        </button>
        @if(auth()->user()->isSuperAdmin())
        <button type="button" onclick="submitBulkDelete()"
                class="bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-red-700 transition">
            Delete Selected
        </button>
        @endif
        <button type="button" onclick="clearSelection()"
                class="text-blue-500 text-sm hover:underline">Clear</button>
    </div>

    <form method="POST" action="{{ route('students.bulk-valid-till.alias') }}" id="bulkForm" class="hidden">
        @csrf
    </form>
    <form method="POST" action="{{ route('students.bulk-learning-accounts') }}" id="bulkLearningForm" class="hidden">
        @csrf
    </form>
    @if(auth()->user()->isSuperAdmin())
    <form method="POST" action="{{ route('students.bulk-destroy') }}" id="bulkDeleteForm" class="hidden">
        @csrf
    </form>
    @endif

    {{-- ── Table ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50 px-4 py-2 text-[11px] font-semibold uppercase tracking-wide text-gray-400 sm:hidden">
            Swipe sideways to view all columns
        </div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] table-fixed text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-3 py-3.5 w-8">
                        <input type="checkbox" id="selectAll" class="accent-primary" title="Select all">
                    </th>
                    <th class="px-4 py-3.5 w-[22%] text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-4 py-3.5 w-[13%] text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Roll / ID</th>
                    <th class="px-4 py-3.5 w-[16%] text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Department / Section</th>
                    <th class="px-4 py-3.5 w-[10%] text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3.5 w-[11%] text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cards</th>
                    <th class="px-4 py-3.5 w-[18%] text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Print Preview</th>
                    <th class="px-4 py-3.5 w-[10%] text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-50">
                @forelse($students as $student)
                @php
                    $typeColors = [
                        'student' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
                        'teacher' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                        'staff'   => ['bg' => 'bg-green-100',  'text' => 'text-green-700'],
                    ];
                    $tc = $typeColors[$student->member_type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                @endphp
                @php
                    $isExpired = $student->valid_till && $student->valid_till->isPast();
                @endphp
                <tr class="transition group {{ $isExpired ? 'bg-red-50/60 hover:bg-red-50' : 'hover:bg-gray-50/70' }}">
                    <td class="px-3 py-3.5">
                        <input type="checkbox" name="ids[]" value="{{ $student->id }}"
                               form="bulkForm"
                               class="row-check accent-primary">
                    </td>
                    {{-- Member info --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}"
                                     class="w-10 h-10 rounded-full object-cover bg-gray-100 ring-2 ring-white shadow-sm">
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate">{{ $student->full_name }}</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs text-gray-400 truncate">{{ $student->email ?: $student->mobile }}</p>
                                    @if($isExpired)
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">
                                        Expired
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Roll number --}}
                    <td class="px-4 py-3.5">
                        <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                            {{ $student->roll_number }}
                        </span>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="text-sm text-gray-700">
                            <p class="font-medium truncate">{{ $student->stream ?: '—' }}</p>
                            <p class="text-xs text-gray-400">Section: {{ $student->section ?: '—' }}</p>
                            <p class="text-xs {{ $isExpired ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                Valid Till: {{ $student->valid_till?->format('d M Y') ?? '—' }}
                            </p>
                        </div>
                    </td>

                    {{-- Type badge --}}
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                                     {{ $tc['bg'] }} {{ $tc['text'] }}">
                            {{ $student->member_type }}
                        </span>
                    </td>

                    {{-- Issued cards --}}
                    <td class="px-4 py-3.5">
                        <div class="flex gap-1 flex-wrap">
                            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full border border-blue-100 font-medium">
                                ID
                            </span>
                            @if($student->user_id)
                            <span class="text-xs bg-green-50 text-green-600 px-2 py-0.5 rounded-full border border-green-100 font-medium">
                                Learning
                            </span>
                            @endif
                        </div>
                    </td>

                    {{-- ── Print Preview column ────────────────────── --}}
                    <td class="px-4 py-3.5">
                        <a href="{{ route('cards.print', [$student, 'id']) }}"
                           target="_blank"
                           title="Print Preview — ID Card"
                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium
                                  bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-100
                                  transition whitespace-nowrap">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4
                                         a2 2 0 0 0 2 2h2m2 4h6a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H9
                                         a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2z"/>
                            </svg>
                            Print ID Card
                        </a>
                    </td>

                    {{-- ── Actions column ──────────────────────────── --}}
                    <td class="px-4 py-3.5">
                        <div class="flex flex-wrap items-center gap-1">

                            <a href="{{ route('students.show', $student) }}"
                               title="View Member"
                               class="p-1.5 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Edit — managed in HR --}}
                            <a href="{{ route('admin.hr.members.edit', $student) }}"
                               title="Edit Member"
                               class="p-1.5 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5
                                             m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('students.destroy', $student) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($student->full_name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete Member"
                                        class="p-1.5 rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7
                                                 m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-16 text-gray-400">
                        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                     M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                     m0 0a5.002 5.002 0 0 1 9.288 0"/>
                        </svg>
                        <p class="font-medium text-gray-500">No members found</p>
                        @if(request('search') || request('type') || request('stream') || request('section'))
                        <p class="text-sm mt-1">
                            Try clearing your filters or
                            <a href="{{ route('students.index') }}" class="text-blue-500 hover:underline">view all members</a>.
                        </p>
                        @else
                        <a href="{{ \App\Services\ModuleService::enabled('hr') && auth()->user()->canAccess('hr.members.create') ? route('admin.hr.members.create') : route('students.create') }}"
                           class="inline-block mt-4 bg-primary text-white px-5 py-2 rounded-lg text-sm hover:bg-primary-light transition">
                            Add from HR
                        </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- ── Pagination ──────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-gray-500">
        <p>
            @if($students->total() > 0)
                Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} members
            @else
                No members found
            @endif
        </p>
        @if($students->hasPages())
        <div>{{ $students->links() }}</div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
{{-- Alpine.js for the dropdown (load only if not already in layout) --}}
@if(!isset($alpineLoaded))
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endif
<script>
    const filterOptions = @json($filterOptions ?? []);
    const selectedStream = @json($selectedStream);
    const selectedSection = @json($selectedSection);
    const filterOrganization = document.getElementById('filterOrganization');
    const filterStream = document.getElementById('filterStream');
    const filterSection = document.getElementById('filterSection');

    function setFilterOptions(select, options, selectedValue, placeholder) {
        if (!select) return;

        select.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = placeholder;
        select.appendChild(defaultOption);

        options.forEach(function(value) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            option.selected = value === selectedValue;
            select.appendChild(option);
        });
    }

    function currentOrganizationKey() {
        if (filterOrganization) return filterOrganization.value;
        return @json($selectedOrganization);
    }

    function refreshFilterSections(selected = '') {
        const organization = filterOptions[currentOrganizationKey()] || { streams: {} };
        const sections = organization.streams[filterStream?.value] || [];
        setFilterOptions(filterSection, sections, selected, 'All Sections');
        if (filterSection) filterSection.disabled = sections.length === 0;
    }

    function refreshFilterStreams(selected = '', section = '') {
        const organization = filterOptions[currentOrganizationKey()] || { streams: {} };
        const streams = Object.keys(organization.streams || {}).sort();
        setFilterOptions(filterStream, streams, selected, 'All Departments / Classes');
        if (filterStream) filterStream.disabled = streams.length === 0;
        refreshFilterSections(section);
    }

    filterOrganization?.addEventListener('change', function() {
        refreshFilterStreams('', '');
    });

    filterStream?.addEventListener('change', function() {
        refreshFilterSections('');
    });

    refreshFilterStreams(selectedStream, selectedSection);

    const bulkBar      = document.getElementById('bulkBar');
    const selectedCount = document.getElementById('selectedCount');
    const selectAll    = document.getElementById('selectAll');
    const rowChecks    = () => document.querySelectorAll('.row-check');

    function updateBulkBar() {
        const checked = [...rowChecks()].filter(c => c.checked);
        if (checked.length > 0) {
            bulkBar.classList.remove('hidden');
            selectedCount.textContent = checked.length + ' selected';
        } else {
            bulkBar.classList.add('hidden');
        }
        selectAll.indeterminate = checked.length > 0 && checked.length < rowChecks().length;
        selectAll.checked = checked.length === rowChecks().length && rowChecks().length > 0;
    }

    function clearSelection() {
        rowChecks().forEach(c => c.checked = false);
        selectAll.checked = false;
        updateBulkBar();
    }

    function submitBulkDelete() {
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        if (!bulkDeleteForm) return;

        const checked = [...rowChecks()].filter(c => c.checked);
        if (checked.length === 0) return;

        if (!confirm('Delete ' + checked.length + ' selected member(s)? Their associated photos will also be deleted. This cannot be undone.')) {
            return;
        }

        bulkDeleteForm.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());

        checked.forEach(function (checkbox) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = checkbox.value;
            bulkDeleteForm.appendChild(input);
        });

        bulkDeleteForm.submit();
    }

    function submitBulkLearningAccounts() {
        const bulkLearningForm = document.getElementById('bulkLearningForm');
        if (!bulkLearningForm) return;

        const checked = [...rowChecks()].filter(c => c.checked);
        if (checked.length === 0) return;

        const password = bulkLearningForm.querySelector('input[name="learning_password"]')?.value || '';
        const confirmation = bulkLearningForm.querySelector('input[name="learning_password_confirmation"]')?.value || '';
        if (password.length < 8 || password !== confirmation) {
            alert('Enter matching learning passwords with at least 8 characters.');
            return;
        }

        bulkLearningForm.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());

        checked.forEach(function (checkbox) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = checkbox.value;
            bulkLearningForm.appendChild(input);
        });

        bulkLearningForm.submit();
    }

    selectAll.addEventListener('change', function () {
        rowChecks().forEach(c => c.checked = this.checked);
        updateBulkBar();
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('row-check')) updateBulkBar();
    });
</script>
@endpush
