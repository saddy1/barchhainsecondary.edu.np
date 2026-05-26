{{-- resources/views/backend/faculty/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-[#0b2415]">Edit Faculty Profile</h2>
        <a href="{{ route('admin.faculty.index') }}" class="text-sm font-bold text-gray-500 hover:text-[#1a5632] transition-colors">
            &larr; Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        <form action="{{ route('admin.faculty.update', $faculty->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $faculty->name) }}" class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-[#1a5632]/20 outline-none border-gray-200" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Role/Position</label>
                    <input type="text" name="role" value="{{ old('role', $faculty->role) }}" class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-[#1a5632]/20 outline-none border-gray-200" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Department</label>
                    <select name="category" class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-[#1a5632]/20 outline-none border-gray-200">
                        @foreach(['Leadership', 'Science', 'Management', 'Hotel Mgmt', 'Kids School', 'Secondary'] as $cat)
                            <option value="{{ $cat }}" {{ $faculty->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Education</label>
                    <input type="text" name="education" value="{{ old('education', $faculty->education) }}" class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-[#1a5632]/20 outline-none border-gray-200" required>
                </div>
            </div>

            <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                <div class="shrink-0">
                    <img src="{{ asset($faculty->image) }}" class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Change Profile Image</label>
                    <input type="file" name="image_file" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#1a5632] file:text-white hover:file:bg-[#0b2415]">
                    <p class="text-[10px] text-gray-400 mt-2 italic">Leave blank to keep the current image.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Display Order</label>
                <input type="number" name="order" value="{{ old('order', $faculty->order) }}" class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-[#1a5632]/20 outline-none border-gray-200">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-[#1a5632] text-white font-bold rounded-xl shadow-lg hover:bg-[#0b2415] hover:-translate-y-0.5 transition-all duration-300">
                    Update Faculty Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection