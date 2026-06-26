@extends('library-admin.layouts.app')

@section('title', 'HR Patrons')

@section('library-content')
<div class="mx-auto max-w-7xl space-y-4">
    <form method="GET" class="w-full sm:w-96">
        <input name="search" value="{{ request('search') }}" placeholder="Search HR student/staff" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold outline-none focus:border-emerald-700">
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="bg-slate-50 text-xs font-black uppercase tracking-widest text-slate-500">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Identifier</th>
                    <th class="px-5 py-3">Class / Section</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($patrons as $patron)
                    <tr>
                        <td class="px-5 py-3 font-black text-slate-950">{{ $patron->student?->full_name ?: $patron->name }}</td>
                        <td class="px-5 py-3 font-semibold text-slate-600">{{ $patron->role_label }}</td>
                        <td class="px-5 py-3 font-semibold text-slate-600">{{ $patron->student_code ?: $patron->student?->registration_no ?: $patron->student?->roll_number ?: '-' }}</td>
                        <td class="px-5 py-3 font-semibold text-slate-600">{{ trim(collect([$patron->class_grade, $patron->section])->filter()->implode(' ')) ?: '-' }}</td>
                        <td class="px-5 py-3 font-semibold text-slate-600">{{ $patron->email ?: '-' }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.library.issue.index', ['borrower' => $patron->student_code ?: $patron->email ?: $patron->name]) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50">Issue</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center font-bold text-slate-400">No HR patrons found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($patrons->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $patrons->links() }}</div>
        @endif
    </div>
</div>
@endsection
