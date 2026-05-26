@extends('layouts.admin')
@section('title', 'Admission Inquiries')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Admission Applications</h2>
            <p class="text-sm text-gray-500 mt-1">Manage and review student enrollment requests.</p>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('admin.admissions.index') }}" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#1a5632] focus:border-[#1a5632] block w-full p-2.5">
                <option value="All" {{ request('status') == 'All' ? 'selected' : '' }}>All Statuses</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Reviewed" {{ request('status') == 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="Accepted" {{ request('status') == 'Accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>
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
                        <th class="px-6 py-4">Student Name</th>
                        <th class="px-6 py-4">Applying For</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($admissions as $app)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $app->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $app->student_name }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-bold">{{ $app->applied_grade }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $app->phone }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                @if($app->status === 'Pending') bg-yellow-100 text-yellow-800
                                @elseif($app->status === 'Reviewed') bg-blue-100 text-blue-800
                                @elseif($app->status === 'Accepted') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.admissions.show', $app->id) }}" class="text-[#1a5632] hover:underline font-bold text-xs">Review &rarr;</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No admission applications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $admissions->links() }}
        </div>
    </div>
</div>
@endsection