@extends('layouts.admin')
@section('title', 'Review Application')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.admissions.index') }}" class="text-sm font-bold text-gray-500 hover:text-[#1a5632]">&larr; Back to Admissions</a>
        
        <form action="{{ route('admin.admissions.destroy', $admission->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this application?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-500 text-sm font-bold hover:underline">🗑 Delete Application</button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-sm font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-8">
        
        {{-- Left: Student Details --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-6 border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-900">Student Information</h2>
                    <span class="bg-[#1a5632]/10 text-[#1a5632] px-3 py-1 rounded-lg text-sm font-bold">Applied for: {{ $admission->applied_grade }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Full Name</p>
                        <p class="text-gray-900 font-medium">{{ $admission->student_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Date of Birth</p>
                        <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($admission->dob)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Gender</p>
                        <p class="text-gray-900 font-medium">{{ $admission->gender }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Previous School</p>
                        <p class="text-gray-900 font-medium">{{ $admission->previous_school ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 border-b pb-4">Guardian & Contact</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Guardian Name</p>
                        <p class="text-gray-900 font-medium">{{ $admission->guardian_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Phone Number</p>
                        <a href="tel:{{ $admission->phone }}" class="text-blue-600 font-bold hover:underline">{{ $admission->phone }}</a>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Email</p>
                        <p class="text-gray-900 font-medium">{{ $admission->email ?: 'N/A' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Address</p>
                        <p class="text-gray-900 font-medium">{{ $admission->address }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Status & Actions --}}
        <div class="md:col-span-1">
            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6 sticky top-24">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Application Status</h3>
                
                <form action="{{ route('admin.admissions.update', $admission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-5">
                        <select name="status" class="w-full border-gray-300 rounded-xl focus:ring-[#1a5632] focus:border-[#1a5632] font-bold p-3
                            @if($admission->status === 'Pending') bg-yellow-50 text-yellow-800
                            @elseif($admission->status === 'Reviewed') bg-blue-50 text-blue-800
                            @elseif($admission->status === 'Accepted') bg-green-50 text-green-800
                            @else bg-red-50 text-red-800 @endif">
                            <option value="Pending" {{ $admission->status == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="Reviewed" {{ $admission->status == 'Reviewed' ? 'selected' : '' }}>👀 Reviewed</option>
                            <option value="Accepted" {{ $admission->status == 'Accepted' ? 'selected' : '' }}>✅ Accepted</option>
                            <option value="Rejected" {{ $admission->status == 'Rejected' ? 'selected' : '' }}>❌ Rejected</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Admin Remarks (Private)</label>
                        <textarea name="admin_remarks" rows="4" class="w-full border-gray-300 rounded-xl p-3 focus:ring-[#1a5632] focus:border-[#1a5632] text-sm" placeholder="Add notes about interviews, calls, or missing documents...">{{ $admission->admin_remarks }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#1a5632] text-white font-bold py-3 rounded-xl hover:bg-[#0b2415] transition-colors shadow-md">
                        Save Changes
                    </button>
                    
                    <p class="text-center text-xs text-gray-400 mt-4">Submitted: {{ $admission->created_at->format('M d, Y h:i A') }}</p>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection