@extends('learning.layouts.admin')

@section('title', 'Student Learning Accounts')

@section('content')
    @php
        $inputClass = 'mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15';
        $compactInputClass = 'w-full sm:w-48 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-gray-800 shadow-sm placeholder:text-gray-400 focus:border-[#1a5632] focus:outline-none focus:ring-2 focus:ring-[#1a5632]/15';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-gray-400">E-Learning</p>
                <h1 class="text-3xl font-extrabold text-gray-950 mt-1">Student Accounts</h1>
                <p class="text-gray-500 mt-2">Issue User IDs and passwords for students to access the learning portal.</p>
            </div>
            <a href="{{ route('admin.learning.dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">Back</a>
        </div>

        

        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if(auth()->user()?->canAccess('hr.members.create') && \App\Services\ModuleService::enabled('hr'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-emerald-950">Student creation is centralized in HR</h2>
                        <p class="mt-1 text-sm font-medium text-emerald-800">Create or import students from HR People Master. Learning uses the linked login account automatically.</p>
                    </div>
                    <a href="{{ route('admin.hr.members.create') }}" class="inline-flex justify-center rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415]">Create in HR</a>
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-gray-500">Student</th>
                            <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-gray-500">User ID</th>
                            <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-gray-500">Class</th>
                            <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-gray-500">Reset Password</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($students as $student)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-extrabold text-gray-900">{{ $student->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $student->email }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-700">{{ $student->student_code }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ $student->class_grade ?? '-' }}{{ $student->section ? ' · '.$student->section : '' }}</td>
                                <td class="px-5 py-4">
                                    @if(auth()->user()?->canAccess('learning.students.edit'))
                                        <form method="POST" action="{{ route('admin.learning.students.password', $student) }}" class="flex flex-col sm:flex-row gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="password" name="password" placeholder="New password" required class="{{ $compactInputClass }}">
                                            <input type="password" name="password_confirmation" placeholder="Confirm" required class="{{ $compactInputClass }}">
                                            <button class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-xs font-extrabold text-gray-700 shadow-sm hover:bg-gray-50">Reset</button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-400">No access</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500">No student accounts created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100">{{ $students->links() }}</div>
        </div>
    </div>
@endsection
