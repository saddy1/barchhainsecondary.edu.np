{{-- resources/views/backend/announcements/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Content')
@section('header_title', 'Notices & Events')

@section('content')

<div class="max-w-7xl mx-auto">
    
    {{-- Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manage Publications</h2>
            <p class="text-sm text-gray-500 mt-1">Create, edit, and manage all your notices, events, and news.</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1a5632] text-white font-bold rounded-xl hover:bg-[#0b2415] shadow-md hover:shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            Create New Post
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-[#1a5632]/20 text-[#1a5632] rounded-xl p-4 text-sm font-bold flex items-center gap-3">
            <span class="w-6 h-6 bg-[#1a5632] text-white rounded-full flex items-center justify-center shrink-0">✓</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-widest">
                        <th class="px-6 py-4 font-bold">Title</th>
                        <th class="px-6 py-4 font-bold">Type</th>
                        <th class="px-6 py-4 font-bold">Category</th>
                        <th class="px-6 py-4 font-bold">Date Posted</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($announcements as $post)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                             {{-- Thumbnail Handle (Image vs PDF vs Drive Link) --}}
<div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden shrink-0 border border-gray-200">
    
    @if($post->image_type === 'link')
        {{-- Display Clickable Link Icon for Drive URLs --}}
        <a href="{{ $post->image_url }}" target="_blank" title="Open Drive Link" class="w-full h-full flex items-center justify-center hover:bg-blue-50 transition-colors">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
        </a>

    @elseif($post->image_type === 'upload' && Str::endsWith(strtolower($post->featured_image), '.pdf'))
        {{-- Display PDF Icon for Local PDFs --}}
<a href="{{ route('admin.announcements.view_file', $post->id) }}" target="_blank" title="View PDF" class="w-full h-full flex items-center justify-center hover:bg-red-50 transition-colors">            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M8.267 14.68c-.184 0-.308.018-.372.036v1.178c.076.018.171.023.302.023.479 0 .774-.242.774-.651 0-.366-.254-.586-.704-.586zm3.487.012c-.2 0-.33.018-.407.036v2.61c.077.018.201.018.313.018.817.006 1.349-.444 1.349-1.396.006-.83-.479-1.268-1.255-1.268z"></path><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.498 16.19c-.309.29-.765.42-1.296.42a2.23 2.23 0 0 1-.308-.018v1.426H7v-3.936A7.558 7.558 0 0 1 8.219 14c.557 0 .954.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zm3.807 1.355c-.42.349-1.059.515-1.84.515-.468 0-.799-.03-1.024-.06v-3.917A7.947 7.947 0 0 1 11.66 14c.757 0 1.249.136 1.633.426.415.308.675.799.675 1.504 0 .763-.279 1.29-.663 1.615zM17 14.77h-1.532v.911H16.9v.734h-1.432v1.604h-.906V14.03H17v.74zM13 9h5l-5-5v5z"></path></svg>
        </a>

    @elseif($post->featured_image)
        {{-- Display Regular Uploaded Image --}}
        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        
    @else
        {{-- Fallback empty state --}}
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
    @endif

</div>
                                <div>
                                    <p class="font-bold text-gray-900 line-clamp-1">{{ $post->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Slug: {{ Str::limit($post->slug, 30) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md
                                @if($post->type === 'event') bg-[#e2a024]/10 text-[#b87e15] border border-[#e2a024]/20
                                @elseif($post->type === 'notice') bg-blue-50 text-blue-700 border border-blue-200
                                @else bg-purple-50 text-purple-700 border border-purple-200
                                @endif">
                                {{ $post->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">{{ $post->category }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $post->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                
                                {{-- Edit Button --}}
                                <a href="{{ route('admin.announcements.edit', $post->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                {{-- Delete Form --}}
                                <form action="{{ route('admin.announcements.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="text-4xl mb-3">📭</div>
                            <p class="font-medium text-gray-900 mb-1">No posts found</p>
                            <p class="text-sm">You haven't published any notices or events yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($announcements->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</div>

@endsection