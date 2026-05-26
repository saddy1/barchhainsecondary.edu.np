@extends('learning.layouts.admin')

@section('title', 'Learning Resources')

@section('content')
@php
    $input = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15';
    $compactInput = 'rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-semibold focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15';
    $types = ['note' => 'Note', 'syllabus' => 'Syllabus', 'old-question' => 'Old Question', 'practice-material' => 'Practice Material'];
    $subjectsMap = $subjects->groupBy('learning_class_id')
        ->map(fn ($group) => $group->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values())
        ->toArray();
@endphp
<div class="space-y-5">
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm">
        <p class="text-sm font-bold uppercase tracking-widest text-white/50">E-Learning</p>
        <h1 class="mt-1 text-3xl font-extrabold">Resources</h1>
        <p class="mt-2 max-w-3xl text-sm font-medium text-white/70">
            Notes, syllabus, old questions, and practice materials. Teachers can create resources only for mapped classes.
        </p>
    </div>
    
    @if($errors->any()) <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div> @endif

    @if($isTeacherScoped && empty($manageableClassIds))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm font-semibold text-amber-800">
            You have learning resource permission, but no class is mapped to your teacher account yet. Ask Principal or Administrator to assign classes from Teacher Mapping.
        </div>
    @endif

    @if(auth()->user()?->canAccess('learning.resources.create') && (! $isTeacherScoped || ! empty($manageableClassIds)))
    <form method="POST" action="{{ route('admin.learning.resources.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm" x-data="{classId: '', published: true}">
        @csrf
        <div class="mb-3 flex flex-col gap-1">
            <h2 class="text-base font-extrabold">Add Resource</h2>
            @if($isTeacherScoped)
                <p class="text-xs font-medium text-gray-500">Only your mapped classes are available for resource creation.</p>
            @endif
        </div>
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <select name="type" required class="{{ $input }}">@foreach($types as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>
            <select name="learning_class_id" x-model="classId" class="{{ $input }}" @if($isTeacherScoped) required @endif>
                @unless($isTeacherScoped)<option value="">All classes</option>@endunless
                @foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach
            </select>
            <select name="learning_subject_id" class="{{ $input }}" :disabled="!classId">
                <option value="" x-text="classId ? 'All subjects' : 'Pick class first'"></option>
                <template x-for="s in (window._resourceSubjectsMap[classId] || [])" :key="s.id">
                    <option :value="s.id" x-text="s.name"></option>
                </template>
            </select>
            <input name="title" required placeholder="Resource title" class="{{ $input }}">
            <input type="file" name="file" class="xl:col-span-2 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#1a5632] file:px-3 file:py-1.5 file:text-sm file:font-bold file:text-white">
            <div>
                <input type="checkbox" name="is_published" value="1" x-model="published" class="sr-only">
                <button type="button" @click="published = !published"
                        class="inline-flex h-full w-full items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-extrabold transition-all"
                        :class="published ? 'border-emerald-200 bg-emerald-50 text-emerald-700 ring-2 ring-emerald-100' : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50'">
                    <span class="flex h-4 w-4 items-center justify-center rounded-full"
                          :class="published ? 'bg-emerald-500 text-white' : 'border border-gray-300 bg-white'">
                        <svg x-show="published" class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    Published
                </button>
            </div>
            <input name="description" placeholder="Description" class="{{ $input }} md:col-span-2 xl:col-span-4">
        </div>
        <button class="mt-3 rounded-xl bg-[#1a5632] px-5 py-2.5 text-sm font-extrabold text-white">Save Resource</button>
    </form>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
        <div class="flex flex-col gap-1 border-b border-gray-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-extrabold text-gray-950">Resource Library</h2>
                <p class="text-xs font-semibold text-gray-500">Compact list with scrollable previews and inline update controls.</p>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ $resources->total() }} resources</span>
        </div>
        <div class="overflow-x-auto cursor-grab active:cursor-grabbing select-none" id="resourcesScroll">
            <table class="min-w-[1250px] w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Resource</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Preview</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Class / Subject</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-gray-500">File</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($resources as $resource)
                    @php
                        $canMutateResource = auth()->user()?->canAccess('learning.resources.edit')
                            && (! $isTeacherScoped || ($resource->created_by === auth()->id() && in_array((int) $resource->learning_class_id, $manageableClassIds ?? [], true)));
                        $canDeleteResource = auth()->user()?->canAccess('learning.resources.delete')
                            && (! $isTeacherScoped || ($resource->created_by === auth()->id() && in_array((int) $resource->learning_class_id, $manageableClassIds ?? [], true)));
                    @endphp
                    <tr class="align-top hover:bg-gray-50/60">
                        <td class="px-4 py-2.5 w-[230px] align-middle">
                            <p class="max-w-[240px] truncate text-sm font-extrabold text-gray-950">{{ $resource->title }}</p>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-extrabold uppercase text-gray-600">{{ $types[$resource->type] ?? $resource->type }}</span>
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-extrabold uppercase {{ $resource->is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $resource->is_published ? 'Published' : 'Draft' }}</span>
                            </div>
                            @if($resource->creator)
                                <p class="mt-2 truncate text-[11px] font-bold text-gray-400">By {{ $resource->creator->name }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 w-[230px] align-middle">
                            <div class="max-h-12 max-w-[230px] overflow-y-auto rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-xs leading-5 text-gray-600 custom-scrollbar">
                                {{ $resource->description ?: 'No description added.' }}
                            </div>
                        </td>
                        <td class="px-4 py-2.5 w-[130px] align-middle">
                            <p class="text-xs font-extrabold text-gray-800">{{ $resource->learningClass->name ?? 'All classes' }}</p>
                            <p class="mt-0.5 max-w-[140px] truncate text-[11px] font-semibold text-gray-500">{{ $resource->subject->name ?? 'All subjects' }}</p>
                        </td>
                        <td class="px-4 py-2.5 w-[90px] align-middle text-xs">
                            @if($resource->file_url)
                                <a href="{{ $resource->file_url }}" target="_blank" class="inline-flex rounded-lg bg-emerald-50 px-2.5 py-1.5 font-extrabold text-[#1a5632] hover:bg-emerald-100">Open</a>
                            @else
                                <span class="text-gray-400">No file</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 align-middle">
                            @if($canMutateResource)
                            <form method="POST" action="{{ route('admin.learning.resources.update', $resource) }}" enctype="multipart/form-data" class="flex min-w-[680px] items-center gap-1.5"
                                  x-data="{classId: '{{ $resource->learning_class_id }}', subjectId: '{{ $resource->learning_subject_id ?? '' }}', published: {{ $resource->is_published ? 'true' : 'false' }}}"
                                  x-init="$watch('classId', () => { subjectId = '' })">
                                @csrf @method('PATCH')
                                <select name="type" class="w-28 shrink-0 {{ $compactInput }}">@foreach($types as $value => $label)<option value="{{ $value }}" @selected($resource->type === $value)>{{ $label }}</option>@endforeach</select>
                                <select name="learning_class_id" x-model="classId" class="w-28 shrink-0 {{ $compactInput }}" @if($isTeacherScoped) required @endif>
                                    @unless($isTeacherScoped)<option value="">All classes</option>@endunless
                                    @foreach($classes as $class)<option value="{{ $class->id }}" @selected($resource->learning_class_id === $class->id)>{{ $class->name }}</option>@endforeach
                                </select>
                                <select name="learning_subject_id" x-model="subjectId" class="w-32 shrink-0 {{ $compactInput }}" :disabled="!classId">
                                    <option value="">All subjects</option>
                                    <template x-for="s in (window._resourceSubjectsMap[classId] || [])" :key="s.id">
                                        <option :value="s.id" x-text="s.name" :selected="s.id == subjectId"></option>
                                    </template>
                                </select>
                                <input name="title" value="{{ $resource->title }}" class="w-36 shrink-0 {{ $compactInput }}">
                                <input name="description" value="{{ $resource->description }}" class="w-40 shrink-0 {{ $compactInput }}" placeholder="Description">
                                <input type="file" name="file" class="w-32 shrink-0 text-xs file:mr-1 file:rounded-md file:border-0 file:bg-gray-100 file:px-2 file:py-1 file:text-xs file:font-bold file:text-gray-600">
                                <input type="checkbox" name="is_published" value="1" x-model="published" class="sr-only">
                                <button type="button" @click="published = !published"
                                        class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-extrabold transition-all"
                                        :class="published ? 'border-emerald-200 bg-emerald-50 text-emerald-700 ring-2 ring-emerald-100' : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50'">
                                    <span class="flex h-3.5 w-3.5 items-center justify-center rounded-full"
                                          :class="published ? 'bg-emerald-500 text-white' : 'border border-gray-300 bg-white'">
                                        <svg x-show="published" class="h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    Published
                                </button>
                                <button class="shrink-0 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-extrabold hover:bg-gray-50">Update</button>
                            </form>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1.5 text-xs font-extrabold text-gray-500">View only</span>
                            @endif
                            @if($canDeleteResource)
                            <form method="POST" action="{{ route('admin.learning.resources.destroy', $resource) }}" class="mt-2"
                                  onsubmit="return confirm('Delete this resource? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-extrabold text-red-600 hover:bg-red-100">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">No resources yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">{{ $resources->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window._resourceSubjectsMap = @json($subjectsMap);

(function () {
    const el = document.getElementById('resourcesScroll');
    if (!el) return;
    let isDown = false, startX, scrollLeft;

    el.addEventListener('mousedown', function (e) {
        // Don't hijack clicks on interactive elements
        if (e.target.closest('a,button,input,select,textarea,label')) return;
        isDown = true;
        startX = e.pageX - el.offsetLeft;
        scrollLeft = el.scrollLeft;
        el.classList.add('cursor-grabbing');
    });
    document.addEventListener('mouseup', function () {
        isDown = false;
        el.classList.remove('cursor-grabbing');
    });
    el.addEventListener('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault();
        el.scrollLeft = scrollLeft - (e.pageX - el.offsetLeft - startX);
    });
    el.addEventListener('mouseleave', function () {
        isDown = false;
        el.classList.remove('cursor-grabbing');
    });
})();
</script>
@endpush
