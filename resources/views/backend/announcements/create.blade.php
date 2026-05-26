{{-- resources/views/backend/announcements/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Create New Post')
@section('header_title', 'Create Notice or Event')

@section('content')

{{-- Include CKEditor via CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Publish New Content</h2>
        <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-medium transition-colors">
            &larr; Back to List
        </a>
    </div>

    {{-- Form wrapped in Alpine component to handle dynamic fields --}}
    <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" 
          x-data="{ postType: 'notice', imageType: 'upload' }" 
          class="space-y-6">
        @csrf

        <div class="grid lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Main Content (WordPress Style) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Title --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Post Title *</label>
                    <input type="text" name="title" required placeholder="Enter compelling title here..."
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] transition-all">
                </div>

                {{-- Rich Text Editor --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Content Body *</label>
                    <textarea name="content" id="editor" placeholder="Write your content here..."></textarea>
                </div>

                {{-- Excerpt --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Short Excerpt</label>
                    <p class="text-xs text-gray-500 mb-2">A brief summary that appears on the frontend cards.</p>
                    <textarea name="excerpt" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] resize-none"></textarea>
                </div>
            </div>

            {{-- Right Column: Settings & Meta --}}
            <div class="space-y-6">
                
                {{-- Publish Settings --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Publish Settings</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Post Type *</label>
                        <select name="type" x-model="postType" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1a5632]">
                            <option value="notice">Notice / Announcement</option>
                            <option value="event">Upcoming Event</option>
                            <option value="news">School News</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category *</label>
                        <select name="category" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1a5632]">
                            <option value="Academic">Academic</option>
                            <option value="Admission">Admission</option>
                            <option value="Event">Event / Festival</option>
                            <option value="Sports">Sports</option>
                            <option value="General">General</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#1a5632] text-white font-bold rounded-xl hover:bg-[#0b2415] shadow-md hover:shadow-lg transition-all flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Publish Content
                    </button>
                </div>

                {{-- Featured Image / PDF Settings --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Featured File (Image/PDF)</h3>
                    
                    {{-- Toggle Upload vs Link --}}
                    <div class="flex bg-gray-100 rounded-lg p-1 mb-4">
                        <button type="button" @click="imageType = 'upload'" :class="imageType === 'upload' ? 'bg-white shadow text-[#1a5632]' : 'text-gray-500'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all">Upload File</button>
                        <button type="button" @click="imageType = 'link'" :class="imageType === 'link' ? 'bg-white shadow text-[#1a5632]' : 'text-gray-500'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all">Drive Link</button>
                    </div>
                    
                    <input type="hidden" name="image_type" :value="imageType">

                    {{-- Upload Field --}}
                    <div x-show="imageType === 'upload'">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 transition-colors">
                            <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-xs w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-green-50 file:text-[#1a5632] hover:file:bg-green-100">
                            <p class="text-[10px] text-gray-400 mt-2">Accepted: JPG, PNG, WEBP, or PDF (Max 5MB)</p>
                        </div>
                    </div>

                    {{-- Drive Link Field --}}
                    <div x-show="imageType === 'link'" style="display: none;">
                        <input type="url" name="image_link" placeholder="Paste Google Drive link here..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1a5632]">
                        <p class="text-[10px] text-gray-400 mt-2">Ensure the Drive link is set to "Anyone with the link can view".</p>
                    </div>
                </div>

                {{-- Dynamic Event Settings (Only visible if Post Type == Event) --}}
                <div x-show="postType === 'event'" x-transition class="bg-[#fdfbf7] p-6 rounded-2xl shadow-sm border border-[#e2a024]/30" style="display: none;">
                    <h3 class="font-bold text-[#0b2415] mb-4 border-b border-[#e2a024]/20 pb-2">Event Specifics</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Event Date</label>
                            <input type="date" name="event_date" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1a5632]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Event Time (e.g. 10:00 AM)</label>
                            <input type="text" name="event_time" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1a5632]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Location</label>
                            <input type="text" name="event_location" placeholder="e.g. School Auditorium" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#1a5632]">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- Initialize CKEditor --}}
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
        })
        .catch(error => {
            console.error(error);
        });
</script>

{{-- Make CKEditor height taller to look like WordPress --}}
<style>
    .ck-editor__editable_inline {
        min-height: 400px;
        border-radius: 0 0 12px 12px !important;
    }
    .ck-toolbar {
        border-radius: 12px 12px 0 0 !important;
        background: #f9fafb !important;
    }
</style>

@endsection