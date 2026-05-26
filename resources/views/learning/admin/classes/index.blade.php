@extends('learning.layouts.admin')

@section('title', 'Learning Classes')

@section('content')
@php $input = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15'; @endphp
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
            <p class="text-sm font-bold uppercase tracking-widest text-gray-400">E-Learning</p>
            <h1 class="text-3xl font-extrabold mt-1">Classes</h1>
            <p class="mt-2 max-w-3xl text-sm font-medium text-white/70">
                E-learning classes are synced from the ID Card master data, so student ID, learning access, and class sections stay centralized.
            </p>
        </div>
    </div>

    
    @if($errors->any()) <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div> @endif

    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-base font-extrabold text-emerald-950">Central Class Source</h2>
                <p class="mt-1 text-sm font-medium text-emerald-800">
                    These classes and sections come from <span class="font-extrabold">ID Card Settings → Departments</span>. Add or edit class/section there first; e-learning will use the same master list.
                </p>
            </div>
            <a href="{{ route('settings.index', ['tab' => 'departments']) }}" class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-white px-4 py-2 text-sm font-extrabold text-emerald-900 hover:bg-emerald-100">
                Manage Master Classes
            </a>
        </div>
        @if(isset($cardClasses) && $cardClasses->isNotEmpty())
            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($cardClasses as $cardClass)
                    <div class="rounded-xl border border-emerald-100 bg-white p-4">
                        <p class="font-extrabold text-gray-950">{{ $cardClass->name }}</p>
                        <p class="mt-1 text-xs font-bold uppercase tracking-widest text-gray-400">{{ $cardClass->organization->name ?? 'School' }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @forelse($cardClass->sections as $section)
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">{{ $section->name }}</span>
                            @empty
                                <span class="text-xs font-semibold text-gray-400">No section yet</span>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if(auth()->user()?->canAccess('learning.courses.create'))
    <form method="POST" action="{{ route('admin.learning.classes.store') }}" class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
        @csrf
        <h2 class="text-lg font-extrabold mb-4">Add Class</h2>
        <div class="grid gap-4 md:grid-cols-[1fr_160px_140px]">
            <input name="name" required placeholder="Class 10" class="{{ $input }}">
            <input type="number" name="sort_order" min="0" placeholder="Sort" class="{{ $input }}">
            <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-bold"><input type="checkbox" name="is_active" value="1" checked class="accent-[#1a5632]"> Active</label>
        </div>
        <button class="mt-4 rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white">Save Class</button>
    </form>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50"><tr><th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Class</th><th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Subjects</th><th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Courses</th><th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($classes as $class)
                <tr>
                    <td class="px-5 py-4"><p class="font-extrabold">{{ $class->name }}</p><p class="text-xs text-gray-500">Sort {{ $class->sort_order }} · {{ $class->is_active ? 'Active' : 'Inactive' }}</p></td>
                    <td class="px-5 py-4 text-sm">{{ $class->subjects_count }}</td>
                    <td class="px-5 py-4 text-sm">{{ $class->courses_count }}</td>
                    <td class="px-5 py-4">
                        @if(auth()->user()?->canAccess('learning.courses.edit'))
                        <form method="POST" action="{{ route('admin.learning.classes.update', $class) }}" class="flex flex-wrap gap-2">
                            @csrf @method('PATCH')
                            <input name="name" value="{{ $class->name }}" class="w-36 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <input type="number" name="sort_order" value="{{ $class->sort_order }}" class="w-20 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <label class="flex items-center gap-1 text-xs font-bold"><input type="checkbox" name="is_active" value="1" @checked($class->is_active)> Active</label>
                            <button class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-extrabold">Update</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">No classes yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
