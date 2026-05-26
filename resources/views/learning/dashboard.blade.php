@extends('learning.layouts.app')

@section('title', 'My Learning')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Student Learning</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-950 mt-1">My Courses</h1>
            <p class="text-sm text-gray-500 mt-1">Continue lessons and practice materials assigned for your class.</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
            <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Class</p>
            <p class="text-sm font-extrabold">{{ auth()->user()->class_grade ?? 'All Classes' }}{{ auth()->user()->section ? ' · Section '.auth()->user()->section : '' }}</p>
        </div>
    </div>

    @if($courses->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
            <h2 class="text-xl font-extrabold text-gray-900">No published courses yet</h2>
            <p class="text-gray-500 mt-2">Courses for your class will appear here after teachers publish them.</p>
        </div>
    @else
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($courses as $course)
                @php $percent = $progress[$course->id] ?? 0; @endphp
                <a href="{{ route('learning.courses.show', $course) }}" class="group rounded-2xl border border-gray-200 bg-white p-5 hover:border-[#1a5632] hover:shadow-lg transition">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-[#1a5632]">{{ $course->learningClass->name ?? 'Class' }}</p>
                            <h2 class="text-lg font-extrabold text-gray-950 mt-2 group-hover:text-[#1a5632]">{{ $course->title }}</h2>
                        </div>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">{{ $course->subject->name ?? 'General' }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-3 line-clamp-2">{{ $course->description ?: 'Course lessons and practice materials.' }}</p>
                    <div class="mt-5">
                        <div class="flex items-center justify-between text-xs font-bold text-gray-500 mb-2">
                            <span>{{ $course->lessons->count() }} lesson{{ $course->lessons->count() === 1 ? '' : 's' }}</span>
                            <span>{{ $percent }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full bg-[#1a5632]" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
