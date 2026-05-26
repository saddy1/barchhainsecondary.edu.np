@extends('learning.layouts.admin')

@section('title', 'Teacher Class Mapping')

@section('content')
@php
    $checked = 'rounded border-gray-300 text-[#1a5632] focus:ring-[#1a5632]';
    $activeTab = $selectedClass ? (string) $selectedClass->id : 'all';
@endphp

<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
        <p class="text-sm font-bold uppercase tracking-widest text-white/50">E-Learning Access</p>
        <h1 class="mt-1 text-3xl font-extrabold">Teacher Class Mapping</h1>
        <p class="mt-2 max-w-3xl text-sm font-medium text-white/70">
            Assign teachers to one or more classes. Teachers can create and manage resources only for assigned classes; other classes remain view-only.
        </p>
    </div>


    
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    {{-- Class Tabs --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.learning.teacher-maps.index', ['tab' => 'all']) }}"
           class="rounded-full px-4 py-2 text-sm font-bold transition-colors
                  {{ $activeTab === 'all' ? 'bg-[#1a5632] text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#1a5632]/40 hover:text-[#1a5632]' }}">
            All Teachers
        </a>
        @foreach($classes as $class)
            <a href="{{ route('admin.learning.teacher-maps.index', ['tab' => $class->id]) }}"
               class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold transition-colors
                      {{ $activeTab === (string) $class->id ? 'bg-[#1a5632] text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#1a5632]/40 hover:text-[#1a5632]' }}">
                {{ $class->name }}
                <span class="rounded-full {{ $activeTab === (string) $class->id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }} px-1.5 py-0.5 text-xs font-extrabold leading-none">
                    {{ $class->teacher_maps_count }}
                </span>
            </a>
        @endforeach
    </div>

    @if($selectedClass)
        {{-- ── Class-centric view ── --}}
        <div class="grid gap-4 lg:grid-cols-[1fr_280px]">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-gray-950">{{ $selectedClass->name }}</h2>
                        <p class="mt-0.5 text-sm font-medium text-gray-500">Check the teachers who should have access to this class.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-extrabold text-emerald-700">
                        {{ $selectedClass->teacher_maps_count }} assigned
                    </span>
                </div>

                @php $assignedTeacherIds = $selectedClass->teachers->pluck('id')->all(); @endphp

                <form method="POST" action="{{ route('admin.learning.teacher-maps.updateByClass', $selectedClass) }}"
                      x-data="{
                          selected: {{ json_encode($assignedTeacherIds) }},
                          toggle(id) {
                              const idx = this.selected.indexOf(id);
                              idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
                          },
                          isSelected(id) { return this.selected.includes(id); }
                      }">
                    @csrf
                    @method('PATCH')

                    {{-- Hidden inputs driven by Alpine so the form always submits the live state --}}
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="user_ids[]" :value="id">
                    </template>

                    <div class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($teachers as $teacher)
                            <button type="button"
                                    @click="toggle({{ $teacher->id }})"
                                    :class="isSelected({{ $teacher->id }})
                                        ? 'border-emerald-400 bg-emerald-50 ring-2 ring-emerald-300'
                                        : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'"
                                    class="relative w-full text-left rounded-xl border px-4 py-3 transition-all duration-150 focus:outline-none">

                                {{-- Checkmark badge --}}
                                <span x-show="isSelected({{ $teacher->id }})"
                                      class="absolute top-2 right-2 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500">
                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <span x-show="!isSelected({{ $teacher->id }})"
                                      class="absolute top-2 right-2 flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                                </span>

                                <div class="flex items-center gap-2.5 pr-6">
                                    <div :class="isSelected({{ $teacher->id }}) ? 'bg-emerald-600' : 'bg-[#1a5632]'"
                                         class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-extrabold text-white transition-colors">
                                        {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold"
                                           :class="isSelected({{ $teacher->id }}) ? 'text-emerald-900' : 'text-gray-900'">
                                            {{ $teacher->name }}
                                        </p>
                                        @if($teacher->assignedLearningClasses->count() > 0)
                                            <p class="truncate text-xs text-gray-400">
                                                {{ $teacher->assignedLearningClasses->pluck('name')->implode(', ') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-3 py-8 text-center text-sm font-medium text-gray-400">No teacher accounts found.</div>
                        @endforelse
                    </div>

                    <div class="border-t border-gray-100 px-5 py-4 flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            <span class="font-bold text-gray-900" x-text="selected.length">{{ count($assignedTeacherIds) }}</span>
                            of {{ $teachers->count() }} teachers assigned
                        </p>
                        <button type="submit"
                                class="rounded-xl bg-[#1a5632] px-5 py-2.5 text-sm font-extrabold text-white hover:bg-[#0b2415] transition-colors">
                            Save Mapping
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-gray-400">Assigned Teachers</p>
                    <div class="mt-3 space-y-2">
                        @forelse($selectedClass->teachers->sortBy('name') as $teacher)
                            <div class="flex items-center gap-2.5 rounded-xl bg-gray-50 px-3 py-2">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#1a5632] text-xs font-extrabold text-white">
                                    {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                </div>
                                <span class="truncate text-sm font-bold text-gray-800">{{ $teacher->name }}</span>
                            </div>
                        @empty
                            <p class="text-sm font-medium text-gray-400">No teachers assigned yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                    <p class="text-sm font-extrabold text-blue-950">How it works</p>
                    <p class="mt-1 text-sm font-medium text-blue-800">
                        Assign class responsibility here. Then assign subjects below so each teacher only manages their own subject's lessons.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Subject Assignment ── --}}
        <div class="space-y-3">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-xl font-extrabold text-gray-900">Subject Assignment</h2>
                <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-extrabold text-violet-700">
                    {{ $classSubjects->count() }} {{ $classSubjects->count() === 1 ? 'subject' : 'subjects' }} in {{ $selectedClass->name }}
                </span>
            </div>
            <p class="text-sm text-gray-500">A teacher needs <strong>both</strong> class access (above) <strong>and</strong> subject access (below) to manage lessons for a subject.</p>

            @forelse($classSubjects as $subject)
                @php $assignedSubjectTeacherIds = $subject->assignedTeachers->pluck('id')->all(); @endphp
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden"
                     x-data="{
                         selected: {{ json_encode($assignedSubjectTeacherIds) }},
                         toggle(id) { const idx = this.selected.indexOf(id); idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1); },
                         isSelected(id) { return this.selected.includes(id); }
                     }">

                    <div class="border-b border-gray-100 px-5 py-3 flex items-center justify-between bg-gray-50/70">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-100 text-sm font-extrabold text-violet-700">
                                {{ strtoupper(substr($subject->name, 0, 1)) }}
                            </span>
                            <div>
                                <p class="font-extrabold text-gray-900 leading-tight">{{ $subject->name }}</p>
                                @if($subject->code)
                                    <p class="text-[11px] text-gray-400 font-semibold">{{ $subject->code }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-extrabold text-violet-700">
                            <span x-text="selected.length">{{ count($assignedSubjectTeacherIds) }}</span> assigned
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.learning.teacher-maps.updateBySubject', $subject) }}">
                        @csrf @method('PATCH')
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="user_ids[]" :value="id">
                        </template>

                        <div class="grid gap-2 p-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            @forelse($selectedClass->teachers as $teacher)
                                <button type="button"
                                        @click="toggle({{ $teacher->id }})"
                                        :class="isSelected({{ $teacher->id }})
                                            ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-300'
                                            : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'"
                                        class="relative flex items-center gap-2.5 rounded-xl border px-3 py-2.5 text-left transition-all duration-150 focus:outline-none">
                                    <span x-show="isSelected({{ $teacher->id }})"
                                          class="shrink-0 flex h-4 w-4 items-center justify-center rounded-full bg-violet-500">
                                        <svg class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <span x-show="!isSelected({{ $teacher->id }})"
                                          class="shrink-0 h-4 w-4 rounded-full border-2 border-gray-300 bg-white"></span>
                                    <span class="truncate text-sm font-bold"
                                          :class="isSelected({{ $teacher->id }}) ? 'text-violet-900' : 'text-gray-700'">
                                        {{ $teacher->name }}
                                    </span>
                                </button>
                            @empty
                                <p class="col-span-4 py-4 text-center text-sm text-gray-400 italic">
                                    Assign teachers to {{ $selectedClass->name }} first (section above).
                                </p>
                            @endforelse
                        </div>

                        <div class="border-t border-gray-100 px-4 py-3 flex justify-end">
                            <button type="submit"
                                    class="rounded-xl bg-violet-700 px-4 py-2 text-xs font-extrabold text-white hover:bg-violet-900 transition-colors">
                                Save Subject Assignment
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-gray-200 px-5 py-6 text-sm text-gray-400 italic">
                    No active subjects found for {{ $selectedClass->name }}. Add subjects under the Subjects section first.
                </div>
            @endforelse
        </div>

    @else
        {{-- ── All-teachers view (original) ── --}}
        <div class="grid gap-4 lg:grid-cols-[1fr_320px]">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-lg font-extrabold text-gray-950">Teachers</h2>
                    <p class="mt-1 text-sm font-medium text-gray-500">Use this screen for final class-level responsibility. Course/resource permissions still come from Set Permission.</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($teachers as $teacher)
                        @php $assignedIds = $teacher->assignedLearningClasses->pluck('id')->all(); @endphp
                        <div class="p-5"
                             x-data="{
                                 selected: {{ json_encode($assignedIds) }},
                                 toggle(id) {
                                     const idx = this.selected.indexOf(id);
                                     idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
                                 },
                                 isSelected(id) { return this.selected.includes(id); }
                             }">

                            <form method="POST" action="{{ route('admin.learning.teacher-maps.update', $teacher) }}">
                                @csrf
                                @method('PATCH')
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="class_ids[]" :value="id">
                                </template>

                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#1a5632] text-sm font-extrabold text-white">
                                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-base font-extrabold text-gray-950">{{ $teacher->name }}</p>
                                            <p class="truncate text-xs font-medium text-gray-400">{{ $teacher->email }}</p>
                                        </div>
                                    </div>
                                    <button type="submit"
                                            class="shrink-0 rounded-xl bg-[#1a5632] px-4 py-2 text-xs font-extrabold text-white hover:bg-[#0b2415] transition-colors">
                                        Save
                                    </button>
                                </div>

                                <div class="mt-3 grid gap-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                                    @foreach($classes as $class)
                                        <button type="button"
                                                @click="toggle({{ $class->id }})"
                                                :class="isSelected({{ $class->id }})
                                                    ? 'border-emerald-400 bg-emerald-50 ring-2 ring-emerald-300 text-emerald-900'
                                                    : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 text-gray-700'"
                                                class="relative rounded-xl border px-3 py-2.5 text-left text-sm font-bold transition-all duration-150 focus:outline-none">
                                            <span x-show="isSelected({{ $class->id }})"
                                                  class="absolute right-1.5 top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500">
                                                <svg class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                            <span x-show="!isSelected({{ $class->id }})"
                                                  class="absolute right-1.5 top-1.5 flex h-4 w-4 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                                            </span>
                                            <span class="block pr-4">{{ $class->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <p class="text-lg font-extrabold text-gray-900">No teacher accounts found.</p>
                            <p class="mt-1 text-sm font-medium text-gray-500">Create teacher accounts from ID Card member creation or Hajiri users first.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-gray-400">Mapped Classes</p>
                    <div class="mt-4 space-y-3">
                        @foreach($classes as $class)
                            <a href="{{ route('admin.learning.teacher-maps.index', ['tab' => $class->id]) }}"
                               class="flex items-center justify-between rounded-xl bg-gray-50 px-3 py-2 hover:bg-emerald-50 hover:text-[#1a5632] transition-colors group">
                                <span class="text-sm font-extrabold text-gray-800 group-hover:text-[#1a5632]">{{ $class->name }}</span>
                                <span class="rounded-full bg-white px-2.5 py-1 text-xs font-extrabold text-gray-500">{{ $class->teacher_maps_count }} teachers</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                    <p class="text-sm font-extrabold text-blue-950">Professional Rule</p>
                    <p class="mt-1 text-sm font-medium text-blue-800">
                        Assign class responsibility here. Then use Set Permission only for module permissions such as create, edit, delete, reports, and quizzes.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
