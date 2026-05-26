{{-- resources/views/backend/contacts/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Contact Inquiries')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ expandedId: null }">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Contact Inquiries</h2>
            <p class="text-sm text-gray-500 mt-1">Manage messages sent from the public website contact form.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-xl border border-gray-200 shadow-sm text-sm font-bold text-gray-600">
            Total Unread: <span class="text-red-500">{{ \App\Models\ContactMessage::where('is_read', false)->count() }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-sm font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold">Sender</th>
                        <th class="px-6 py-4 font-bold">Topic</th>
                        <th class="px-6 py-4 font-bold">Date Received</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($messages as $msg)
                    {{-- Row --}}
                    <tr class="hover:bg-gray-50 transition-colors {{ $msg->is_read ? 'opacity-70' : 'bg-green-50/30' }}">
                        <td class="px-6 py-4">
                            @if(!$msg->is_read)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> New
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-500 uppercase tracking-widest">
                                    Read
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $msg->name }}</p>
                            <p class="text-xs text-gray-500">{{ $msg->phone }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-[#1a5632]">{{ $msg->subject }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $msg->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- Alpine toggle for reading message --}}
                                <button @click="expandedId === {{ $msg->id }} ? expandedId = null : expandedId = {{ $msg->id }}" class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                                    View
                                </button>
                                
                                @if(!$msg->is_read)
                                <form action="{{ route('admin.contacts.read', $msg->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 bg-green-50 border border-green-200 rounded-lg text-xs font-bold text-green-700 hover:bg-green-100 transition-colors">Mark Read</button>
                                </form>
                                @endif

                                <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message permanently?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg text-xs font-bold text-red-600 hover:bg-red-100 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                    {{-- Expandable Message Area --}}
                    <tr x-show="expandedId === {{ $msg->id }}" class="bg-gray-50 border-b-2 border-gray-200" style="display: none;" x-collapse>
                        <td colspan="5" class="px-6 py-6">
                            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-inner">
                                <div class="grid sm:grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-100">
                                    <div>
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Email Address</span>
                                        <p class="text-sm font-medium text-gray-900 mt-1">
                                            @if($msg->email) <a href="mailto:{{ $msg->email }}" class="text-blue-600 hover:underline">{{ $msg->email }}</a> @else N/A @endif
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Phone Number</span>
                                        <p class="text-sm font-medium text-gray-900 mt-1">
                                            <a href="tel:{{ $msg->phone }}" class="text-blue-600 hover:underline">{{ $msg->phone }}</a>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-2 block">Message Content</span>
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-500 font-medium">No contact messages received yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($messages->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $messages->links('pagination::tailwind') }}
        </div>
        @endif
        
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush