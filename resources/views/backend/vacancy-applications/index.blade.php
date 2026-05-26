@extends('layouts.admin')
@section('title', 'Applications — ' . $vacancy->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.vacancies.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#1a5632] font-medium mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Vacancies
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Applications for: <span class="text-[#1a5632]">{{ $vacancy->title }}</span></h2>
        <p class="text-sm text-gray-500 mt-1">{{ $applications->total() }} total application(s) received.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-sm font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Applicant</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Qualification</th>
                        <th class="px-6 py-4">CV</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $app)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $app->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $app->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $app->email }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $app->phone }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-bold">{{ $app->qualification }}</span>
                            @if($app->experience)
                            <p class="text-xs text-gray-500 mt-1">{{ $app->experience }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ asset('storage/' . $app->cv_path) }}" target="_blank" download
                               class="inline-flex items-center gap-1 text-[#1a5632] hover:text-[#0b2415] font-bold text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download CV
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                @if($app->status === 'Pending') bg-yellow-100 text-yellow-800
                                @elseif($app->status === 'Reviewed') bg-blue-100 text-blue-800
                                @elseif($app->status === 'Shortlisted') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.vacancy-applications.show', $app->id) }}" class="text-[#1a5632] hover:underline font-bold text-xs">Review &rarr;</a>
                                <form action="{{ route('admin.vacancy-applications.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Delete this application?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline font-bold text-xs">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">No applications received yet for this vacancy.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection
