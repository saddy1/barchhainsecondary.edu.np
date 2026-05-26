@extends('learning.layouts.admin')

@section('title', 'Learning Subjects')

@section('content')
@php $input = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15'; @endphp
<div class="space-y-6">
    <div><p class="text-sm font-bold uppercase tracking-widest text-gray-400">E-Learning</p><h1 class="text-3xl font-extrabold text-gray-950 mt-1">Subjects</h1></div>
    
    @if($errors->any()) <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div> @endif

    @if(auth()->user()?->canAccess('learning.courses.create'))
    <form method="POST" action="{{ route('admin.learning.subjects.store') }}" class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
        @csrf
        <h2 class="text-lg font-extrabold mb-4">Add Subject</h2>
        <div class="grid gap-4 md:grid-cols-[220px_1fr_160px_140px]">
            <select name="learning_class_id" required class="{{ $input }}"><option value="">Select class</option>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
            <input name="name" required placeholder="Science" class="{{ $input }}">
            <input name="code" placeholder="SCI" class="{{ $input }}">
            <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-bold"><input type="checkbox" name="is_active" value="1" checked class="accent-[#1a5632]"> Active</label>
        </div>
        <button class="mt-4 rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white">Save Subject</button>
    </form>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50"><tr><th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Subject</th><th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Class</th><th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subjects as $subject)
                <tr>
                    <td class="px-5 py-4"><p class="font-extrabold">{{ $subject->name }}</p><p class="text-xs text-gray-500">{{ $subject->code ?: 'No code' }} · {{ $subject->is_active ? 'Active' : 'Inactive' }}</p></td>
                    <td class="px-5 py-4 text-sm">{{ $subject->learningClass->name ?? '-' }}</td>
                    <td class="px-5 py-4">
                        @if(auth()->user()?->canAccess('learning.courses.edit'))
                        <form method="POST" action="{{ route('admin.learning.subjects.update', $subject) }}" class="flex flex-wrap gap-2">
                            @csrf @method('PATCH')
                            <select name="learning_class_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">@foreach($classes as $class)<option value="{{ $class->id }}" @selected($subject->learning_class_id === $class->id)>{{ $class->name }}</option>@endforeach</select>
                            <input name="name" value="{{ $subject->name }}" class="w-36 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <input name="code" value="{{ $subject->code }}" class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <label class="flex items-center gap-1 text-xs font-bold"><input type="checkbox" name="is_active" value="1" @checked($subject->is_active)> Active</label>
                            <button class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-extrabold">Update</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-10 text-center text-gray-500">No subjects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
