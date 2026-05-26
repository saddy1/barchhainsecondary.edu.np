@extends('learning.layouts.admin')

@section('title', 'Learning Courses')

@php
    $input = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15';

    // Build subjects map keyed by class id for Alpine filtering
    $subjectsMap = $subjects->groupBy('learning_class_id')
        ->map(fn ($group) => $group->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values())
        ->toArray();
@endphp

@push('scripts')
<script>window._subjectsMap = @json($subjectsMap);</script>
<script>
(function () {
    const el = document.getElementById('coursesScroll');
    if (!el) return;
    let isDown = false, startX, scrollLeft;
    el.addEventListener('mousedown', function (e) {
        if (e.target.closest('a,button,input,select,textarea,label')) return;
        isDown = true; startX = e.pageX - el.offsetLeft; scrollLeft = el.scrollLeft;
        el.classList.add('cursor-grabbing');
    });
    document.addEventListener('mouseup', function () { isDown = false; el.classList.remove('cursor-grabbing'); });
    el.addEventListener('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault();
        el.scrollLeft = scrollLeft - (e.pageX - el.offsetLeft - startX);
    });
    el.addEventListener('mouseleave', function () { isDown = false; el.classList.remove('cursor-grabbing'); });
})();
</script>
@endpush

@section('content')
<div class="space-y-6">

    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
        <p class="text-sm font-bold uppercase tracking-widest text-white/50">E-Learning</p>
        <h1 class="mt-1 text-3xl font-extrabold">Courses</h1>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    {{-- ── Add Course Form ── --}}
    @if(auth()->user()?->canAccess('learning.courses.create'))
    <form method="POST" action="{{ route('admin.learning.courses.store') }}"
          class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm"
          x-data="{classId: ''}">
        @csrf
        <h2 class="text-lg font-extrabold mb-4">Add Course / Syllabus Unit</h2>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            {{-- Class --}}
            <select name="learning_class_id" x-model="classId" required class="{{ $input }}">
                <option value="">Select class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>

            {{-- Subject — filtered by selected class --}}
            <div class="relative">
                <select name="learning_subject_id" class="{{ $input }}" :disabled="!classId">
                    <option value="" x-text="classId ? 'General (no subject)' : '— pick a class first —'"></option>
                    <template x-for="s in (window._subjectsMap[classId] || [])" :key="s.id">
                        <option :value="s.id" x-text="s.name"></option>
                    </template>
                </select>
            </div>

            <input name="title" required placeholder="Unit 1: Force" class="{{ $input }}">

            <select name="status" class="{{ $input }}">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>

            <input type="number" name="sort_order" min="0" placeholder="Sort order" class="{{ $input }}">

            <textarea name="description" rows="3" placeholder="Short description"
                      class="{{ $input }} md:col-span-2 xl:col-span-3"></textarea>
        </div>
        <button class="mt-4 rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415] transition-colors">
            Save Course
        </button>
    </form>
    @endif

    {{-- ── Course Table ── --}}
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto cursor-grab active:cursor-grabbing select-none" id="coursesScroll">
            <table class="min-w-[1500px] w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Class / Subject</th>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-extrabold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $course)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3 align-middle w-[230px]">
                            <p class="max-w-[220px] truncate text-sm font-extrabold text-gray-950">{{ $course->title }}</p>
                            <p class="mt-0.5 max-w-[220px] truncate text-xs text-gray-500">{{ $course->description ?: 'No description' }}</p>
                        </td>
                        <td class="px-4 py-3 align-middle text-sm w-[150px]">
                            <p class="truncate font-semibold text-gray-900">{{ $course->learningClass->name ?? '—' }}</p>
                            <p class="truncate text-gray-500">{{ $course->subject->name ?? 'General' }}</p>
                        </td>
                        <td class="px-4 py-3 align-middle w-[120px]">
                            <span class="rounded-full px-3 py-1 text-xs font-bold
                                {{ $course->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 align-middle">
                            <div class="flex min-w-[930px] items-center gap-2">
                                @if(($manageableCourseIds ?? null) === null || in_array($course->id, $manageableCourseIds))
                                    <a href="{{ route('admin.learning.courses.manage', $course) }}"
                                       class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-[#1a5632] px-3 py-2 text-xs font-extrabold text-white hover:bg-[#0b2415] transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                        </svg>
                                        Manage
                                    </a>
                                @endif
                            @if(auth()->user()?->canAccess('learning.courses.edit'))
                            <form method="POST" action="{{ route('admin.learning.courses.update', $course) }}"
                                  class="contents"
                                  x-data="{
                                      classId: '{{ $course->learning_class_id }}',
                                      subjectId: '{{ $course->learning_subject_id ?? '' }}'
                                  }"
                                  x-init="$watch('classId', () => { subjectId = '' })">
                                @csrf @method('PATCH')

                                <select name="learning_class_id" x-model="classId"
                                        class="w-28 shrink-0 rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold">
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected($course->learning_class_id === $class->id)>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="learning_subject_id" x-model="subjectId"
                                        class="w-32 shrink-0 rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold">
                                    <option value="">General</option>
                                    <template x-for="s in (window._subjectsMap[classId] || [])" :key="s.id">
                                        <option :value="s.id" x-text="s.name"
                                                :selected="s.id == subjectId"></option>
                                    </template>
                                </select>

                                <input name="title" value="{{ $course->title }}"
                                       class="w-40 shrink-0 rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold">

                                <select name="status" class="w-28 shrink-0 rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold">
                                    <option value="draft" @selected($course->status === 'draft')>Draft</option>
                                    <option value="published" @selected($course->status === 'published')>Published</option>
                                </select>

                                <input name="description" value="{{ $course->description }}"
                                       class="w-48 shrink-0 rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold"
                                       placeholder="Description">

                                <input type="number" name="sort_order" value="{{ $course->sort_order }}"
                                       class="w-20 shrink-0 rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold"
                                       placeholder="Order">

                                <button class="shrink-0 rounded-lg border border-gray-300 px-3 py-2 text-xs font-extrabold hover:bg-gray-50">
                                    Update
                                </button>
                            </form>

                            @if(auth()->user()?->canAccess('learning.courses.delete'))
                                <form method="POST" action="{{ route('admin.learning.courses.destroy', $course) }}"
                                      class="shrink-0"
                                      onsubmit="return confirm('Delete this course and all its chapters and lessons?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-200 px-3 py-2 text-xs font-extrabold text-red-600 hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            @endif
                            @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-500">No courses yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">{{ $courses->links() }}</div>
    </div>
</div>
@endsection
