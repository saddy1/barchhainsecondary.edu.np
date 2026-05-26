@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        <h2 class="text-2xl font-bold text-[#0b2415] mb-6">Add Faculty Member</h2>

        <form action="{{ route('admin.faculty.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-[#1a5632]" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Role/Position</label>
                    <input type="text" name="role" placeholder="e.g. Principal" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Department/Category</label>
                    <select name="category" class="w-full px-4 py-2 border rounded-lg">
                        <option value="Leadership">Leadership</option>
                        <option value="Science">Science</option>
                        <option value="Management">Management</option>
                        <option value="Kids School">Kids School</option>
                        <option value="Secondary">Secondary</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Education</label>
                    <input type="text" name="education" placeholder="e.g. M.Sc. Physics" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Profile Image</label>
                <input type="file" name="image_file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-green-50 file:text-[#1a5632]">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Display Order (Lower numbers show first)</label>
                <input type="number" name="order" value="0" class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-[#1a5632] text-white font-bold rounded-xl shadow-lg hover:bg-[#0b2415] transition-all">Save Faculty Member</button>
            </div>
        </form>
    </div>
</div>
@endsection