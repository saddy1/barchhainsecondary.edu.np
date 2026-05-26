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

    <form method="GET" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 md:grid-cols-[1fr_180px_120px]">
            <input name="search" value="{{ request('search') }}" placeholder="Search name, ID, email, mobile..." class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
            <select name="type" class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15">
                <option value="">All types</option>
                @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-[#1a5632] px-4 py-3 text-sm font-extrabold text-white">Filter</button>
        </div>
    </form>

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

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-widest text-gray-500">Member</th>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-widest text-gray-500">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-widest text-gray-500">Class / Section</th>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-widest text-gray-500">Linked Modules</th>
                        <th class="px-5 py-3 text-right text-xs font-extrabold uppercase tracking-widest text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($members as $member)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $member->photo_url }}" alt="{{ $member->full_name }}" class="h-11 w-11 rounded-full object-cover ring-1 ring-gray-200">
                                    <div>
                                        <p class="font-extrabold text-gray-950">{{ $member->full_name }}</p>
                                        <p class="text-sm font-medium text-gray-500">{{ $member->roll_number }} · {{ $member->email ?: 'No email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-extrabold {{ $typeStyles[$member->member_type] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $typeLabels[$member->member_type] ?? ucfirst($member->member_type) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-gray-700">
                                {{ $member->stream ?: '—' }}
                                <span class="text-gray-400">/</span>
                                {{ $member->section ?: '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">ID Card</span>
                                    @if($member->user)
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Login</span>
                                        @if($member->user->device_id)
                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Hajiri</span>
                                        @endif
                                        @if($member->user->hasAnyRole(['student', 'teacher']))
                                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">Learning</span>
                                        @endif
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-500">No Login</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @if(auth()->user()?->canAccess('hr.members.edit'))
                                        <a href="{{ route('admin.hr.members.edit', $member) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-extrabold text-gray-700 hover:bg-gray-50">Edit</a>
                                    @endif
                                    @if(auth()->user()?->canAccess('hr.members.delete'))
                                        <form method="POST" action="{{ route('admin.hr.members.destroy', $member) }}" onsubmit="return confirm('Remove this member from HR master?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-extrabold text-red-600 hover:bg-red-100">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="font-extrabold text-gray-900">No HR members yet.</p>
                                <p class="mt-1 text-sm text-gray-500">Create the first student, teacher, or staff member from HR.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-5 py-4">{{ $members->links() }}</div>
    </div>
</div>
@endsection
