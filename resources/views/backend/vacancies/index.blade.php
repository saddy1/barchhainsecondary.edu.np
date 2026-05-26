@extends('layouts.admin')
@section('title', 'Vacancy Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Vacancy Management</h2>
            <p class="text-sm text-gray-500 mt-1">Post and manage job openings for the school.</p>
        </div>
        <a href="{{ route('admin.vacancies.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1a5632] text-white font-bold text-sm rounded-xl hover:bg-[#0b2415] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Post New Vacancy
        </a>
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
                        <th class="px-6 py-4">Position</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Deadline</th>
                        <th class="px-6 py-4">Applications</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vacancies as $vacancy)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $vacancy->title }}</p>
                            @if($vacancy->department)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $vacancy->department }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-bold">{{ $vacancy->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($vacancy->deadline)
                                <span class="{{ $vacancy->isExpired() ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                    {{ $vacancy->deadline->format('M d, Y') }}
                                    @if($vacancy->isExpired()) <span class="text-xs">(Expired)</span>@endif
                                </span>
                            @else
                                <span class="text-gray-400">No deadline</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.vacancies.applications', $vacancy->id) }}"
                               class="inline-flex items-center gap-1 font-bold text-[#1a5632] hover:underline">
                                {{ $vacancy->applications_count }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.vacancies.toggle', $vacancy->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                    {{ $vacancy->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $vacancy->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if($vacancy->document_path)
                                <a href="{{ asset('storage/' . $vacancy->document_path) }}" target="_blank"
                                   class="text-[#e2a024] hover:text-[#b07d10] font-bold text-xs" title="Download Document">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                                @endif
                                <a href="{{ route('admin.vacancies.edit', $vacancy->id) }}" class="text-[#1a5632] hover:underline font-bold text-xs">Edit</a>
                                <form action="{{ route('admin.vacancies.destroy', $vacancy->id) }}" method="POST" onsubmit="return confirm('Delete this vacancy? All applications will also be deleted.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline font-bold text-xs">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No vacancies posted yet. <a href="{{ route('admin.vacancies.create') }}" class="text-[#1a5632] font-bold hover:underline">Post one now</a>.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $vacancies->links() }}
        </div>
    </div>
</div>
@endsection
