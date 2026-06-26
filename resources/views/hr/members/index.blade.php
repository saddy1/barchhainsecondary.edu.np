@extends('hr.layouts.app')

@section('title', 'HR Members')

@section('content')
@php
    $typeLabels = ['student' => 'Student', 'teacher' => 'Teacher', 'staff' => 'Staff'];
    $typeStyles = [
        'student' => 'bg-blue-50 text-blue-700 border-blue-100',
        'teacher' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'staff' => 'bg-amber-50 text-amber-700 border-amber-100',
    ];
@endphp

<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-white/50">Human Resource</p>
                <h1 class="mt-1 text-3xl font-extrabold">People Master</h1>
                <p class="mt-2 max-w-3xl text-sm font-medium text-white/70">
                    Add students, teachers, and staff once. HR syncs them to ID Card, Hajiri, Learning, and future ERP modules.
                </p>
            </div>
            @if(auth()->user()?->canAccess('hr.members.create'))
                <a href="{{ route('admin.hr.members.import') }}" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-extrabold text-white hover:bg-white/20">
                    Bulk Import
                </a>
                <a href="{{ route('admin.hr.members.create') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-extrabold text-[#1a5632] hover:bg-gray-100">
                    New Member
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($counts as $key => $count)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">{{ $key === 'all' ? 'All Members' : $typeLabels[$key] }}</p>
                <p class="mt-2 text-3xl font-black text-gray-950">{{ $count }}</p>
            </div>
        @endforeach
    </div>

    <form id="hr-member-filter-form" method="GET" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm" x-data="districtFilter()">
        <div class="grid gap-3">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[1.8fr_1fr_1fr_1fr_1fr]">
                <input name="search" value="{{ request('search') }}" placeholder="Search name, ID, email, mobile..." autocomplete="off" data-ajax-search class="w-full min-w-0 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">

                <select name="type" class="w-full min-w-0 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    <option value="">All types</option>
                    @foreach($typeLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="stream" class="w-full min-w-0 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    <option value="">All classes</option>
                    @foreach($streams ?? [] as $stream)
                        <option value="{{ $stream }}" @selected(request('stream') === $stream)>{{ $stream }}</option>
                    @endforeach
                </select>

                <select name="section" class="w-full min-w-0 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    <option value="">All sections</option>
                    @foreach($sections ?? [] as $section)
                        <option value="{{ $section }}" @selected(request('section') === $section)>{{ $section }}</option>
                    @endforeach
                </select>

                <select name="per_page" class="w-full min-w-0 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                    @foreach([10,20,50,100] as $p)
                        <option value="{{ $p }}" @selected((int)request('per_page', 20) === $p)>{{ $p }} per page</option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-3 md:grid-cols-3">
                <select name="permanent_district" class="w-full min-w-0 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15" @change="loadMunicipalities($el.value)" x-ref="district">
                    <option value="">All districts</option>
                    @foreach($districts ?? [] as $d)
                        <option value="{{ $d }}" @selected(request('permanent_district') === $d)>{{ $d }}</option>
                    @endforeach
                </select>

                <select name="permanent_municipality" class="w-full min-w-0 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15" x-ref="municipality">
                    <option value="">All municipalities</option>
                    @foreach($municipalities ?? [] as $m)
                        <option value="{{ $m }}" @selected(request('permanent_municipality') === $m)>{{ $m }}</option>
                    @endforeach
                </select>

                <button class="w-full rounded-xl bg-[#1a5632] px-4 py-3 text-sm font-extrabold text-white">Filter</button>
            </div>
        </div>
    </form>

    <script>
        function districtFilter() {
            return {
                loadMunicipalities(district) {
                    if (!district) {
                        this.$refs.municipality.innerHTML = '<option value="">All municipalities</option>';
                        return;
                    }

                    fetch(`/api/hr/municipalities-by-district/${encodeURIComponent(district)}`)
                        .then(res => res.json())
                        .then(municipalities => {
                            let options = '<option value="">All municipalities</option>';
                            municipalities.forEach(m => {
                                const selected = '{{ request("permanent_municipality") }}' === m ? ' selected' : '';
                                options += `<option value="${m}"${selected}>${m}</option>`;
                            });
                            this.$refs.municipality.innerHTML = options;
                        })
                        .catch(err => console.error('Error loading municipalities:', err));
                }
            };
        }
    </script>

    @if($orphanUsers->isNotEmpty())
        <div class="rounded-2xl border border-amber-200 bg-amber-50 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-200 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-extrabold text-amber-900">{{ $orphanUsers->count() }} teacher/staff {{ Str::plural('account', $orphanUsers->count()) }} not yet in HR</p>
                    <p class="mt-0.5 text-xs font-medium text-amber-700">These users were created via Hajiri. Create an HR profile for each to manage them here.</p>
                </div>
            </div>
            <div class="divide-y divide-amber-100">
                @foreach($orphanUsers as $user)
                    @php $role = $user->roles->firstWhere('name', 'teacher') ? 'teacher' : 'staff'; @endphp
                    <div class="flex items-center justify-between gap-4 px-5 py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-200 text-sm font-extrabold text-amber-900">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-extrabold text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs font-medium text-gray-500 truncate">{{ $user->email }}{{ $user->device_id ? ' · Hajiri device #' . $user->device_id : '' }}</p>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <span class="rounded-full border px-2.5 py-1 text-xs font-extrabold {{ $typeStyles[$role] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                {{ $typeLabels[$role] ?? ucfirst($role) }}
                            </span>
                            @if(auth()->user()?->canAccess('hr.members.create'))
                                <a href="{{ route('admin.hr.members.create') }}?prefill_user={{ $user->id }}"
                                   class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-extrabold text-amber-800 hover:bg-amber-100 transition-colors">
                                    Create HR Profile
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div id="hr-member-results">
        @include('hr.members._table', ['members' => $members])
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('hr-member-filter-form');
    const results = document.getElementById('hr-member-results');
    if (!form || !results) return;

    const searchInput = form.querySelector('[data-ajax-search]');
    let searchTimer = null;
    let controller = null;

    const escapeRegex = value => String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    const highlightMatches = () => {
        const query = String(searchInput?.value || '').trim();
        results.querySelectorAll('[data-highlight]').forEach(element => {
            const text = element.textContent || '';
            if (!query) {
                element.textContent = text;
                return;
            }

            const pattern = new RegExp(`(${escapeRegex(query)})`, 'ig');
            element.innerHTML = text.replace(pattern, '<mark class="rounded bg-yellow-200 px-0.5 font-black text-gray-950">$1</mark>');
        });
    };

    const currentUrl = pageUrl => {
        const params = new URLSearchParams(new FormData(form));
        [...params.entries()].forEach(([key, value]) => {
            if (value === '') params.delete(key);
        });
        if (pageUrl) {
            const pageParams = new URL(pageUrl, window.location.origin).searchParams;
            const page = pageParams.get('page');
            if (page) params.set('page', page);
        }
        const qs = params.toString();
        return `${form.action || window.location.pathname}${qs ? `?${qs}` : ''}`;
    };

    const loadMembers = async (pageUrl = null) => {
        if (controller) controller.abort();
        controller = new AbortController();

        const url = currentUrl(pageUrl);
        results.classList.add('opacity-60');

        try {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller.signal,
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const payload = await response.json();
            results.innerHTML = payload.html || '';
            window.history.replaceState({}, '', url);
            highlightMatches();
        } catch (error) {
            if (error.name !== 'AbortError') console.error('Unable to load HR members:', error);
        } finally {
            results.classList.remove('opacity-60');
        }
    };

    form.addEventListener('submit', event => {
        event.preventDefault();
        loadMembers();
    });

    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadMembers(), 250);
    });

    form.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => loadMembers());
    });

    results.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || !link.closest('nav')) return;
        event.preventDefault();
        loadMembers(link.href);
    });

    highlightMatches();
});
</script>
@endpush
