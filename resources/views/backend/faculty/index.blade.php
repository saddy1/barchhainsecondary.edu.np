@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Faculty Management</h2>
        <a href="{{ route('admin.faculty.create') }}" class="bg-[#1a5632] text-white px-4 py-2 rounded-lg font-bold">Add New Teacher</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Member</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Department</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Order</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($faculties as $teacher)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 flex items-center gap-3">
                        <img src="{{ asset($teacher->image) }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-bold text-gray-900">{{ $teacher->name }}</p>
                            <p class="text-xs text-gray-500">{{ $teacher->role }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $teacher->category }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $teacher->order }}</td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <a href="{{ route('admin.faculty.edit', $teacher->id) }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded">Edit</a>
                        <form action="{{ route('admin.faculty.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Delete this teacher?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:bg-red-50 p-2 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection