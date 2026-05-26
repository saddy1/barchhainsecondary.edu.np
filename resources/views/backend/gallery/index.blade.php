{{-- resources/views/backend/gallery/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div x-data="{ lbSrc: '', lbCaption: '', lbOpen: false }"
     @open-preview.window="lbSrc = $event.detail.src; lbCaption = $event.detail.caption; lbOpen = true"
     @keydown.escape.window="lbOpen = false">

    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Media Gallery</h2>
        <p class="text-sm text-gray-500 mt-1">Manage all uploaded images. These can be used in notices, events, and pages.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-[#1a5632]/20 text-[#1a5632] rounded-xl p-4 text-sm font-bold flex items-center gap-3">
            <span class="w-6 h-6 bg-[#1a5632] text-white rounded-full flex items-center justify-center shrink-0">✓</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 text-sm font-bold">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Drag & Drop Upload Zone --}}
    <div x-data="{
            isDropping: false,
            isUploading: false,
            handleFileSelect() {
                if(this.$refs.fileInput.files.length > 0) {
                    this.isUploading = true;
                    this.$refs.uploadForm.submit();
                }
            }
         }"
         class="mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">

        <form x-ref="uploadForm" action="{{ route('admin.gallery.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">1. Select Photo Category</label>
                <select name="category" class="w-full md:w-1/3 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] outline-none transition-all">
                    <option value="Campus">Campus</option>
                    <option value="Events">Events</option>
                    <option value="Academics">Academics</option>
                    <option value="Sports">Sports</option>
                    <option value="Cultural">Cultural</option>
                </select>
            </div>

            <label class="block text-sm font-bold text-gray-700 mb-2">2. Upload Photos</label>
            <div class="relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-2xl transition-colors duration-300"
                 :class="isDropping ? 'border-[#1a5632] bg-green-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
                 @dragover.prevent="isDropping = true"
                 @dragleave.prevent="isDropping = false"
                 @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; handleFileSelect()">

                <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                    <svg x-show="!isUploading" class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <svg x-show="isUploading" style="display: none;" class="animate-spin w-10 h-10 mb-3 text-[#1a5632]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="mb-2 text-sm text-gray-500" x-show="!isUploading"><span class="font-bold text-[#1a5632]">Click to upload</span> or drag and drop</p>
                    <p class="text-xs text-gray-400" x-show="!isUploading">PNG, JPG, WEBP (Max 5MB per file)</p>
                    <p class="text-sm font-bold text-[#1a5632]" x-show="isUploading" style="display: none;">Uploading your images...</p>
                </div>

                <input x-ref="fileInput" @change="handleFileSelect" type="file" name="files[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" :disabled="isUploading" />
            </div>
        </form>
    </div>

    {{-- Gallery Grid --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-4">Previously Uploaded Media</h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($media as $image)
                <div x-data="{ editing: false, caption: @json($image->caption) }"
                     class="group relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shadow-sm hover:shadow-md transition-all">

                    {{-- Image — click opens lightbox --}}
                    <img src="{{ $image->url }}"
                         alt="{{ $image->caption ?? $image->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 cursor-zoom-in"
                         @click="$dispatch('open-preview', { src: '{{ $image->url }}', caption: @json($image->caption ?? '') })">

                    {{-- Hover Overlay (edit/delete actions) --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center p-2 pointer-events-none">
                        <div class="pointer-events-auto w-full flex flex-col items-center">
                            <p class="text-white/60 text-[9px] mb-1">{{ number_format($image->size / 1024, 1) }} KB</p>

                            {{-- Caption display when set --}}
                            <template x-if="caption && !editing">
                                <p class="text-white text-[10px] text-center font-semibold mb-2 line-clamp-2 px-2" x-text="caption"></p>
                            </template>

                            <div class="flex flex-wrap items-center justify-center gap-1.5 mt-1">
                                {{-- Edit caption toggle --}}
                                <button type="button"
                                        @click.stop="editing = !editing"
                                        class="inline-flex items-center gap-1 bg-white/15 hover:bg-white/30 text-white px-2.5 py-1.5 rounded-full text-[10px] font-semibold shadow"
                                        title="Edit caption">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/></svg>
                                    <span x-text="editing ? 'Cancel' : 'Edit'"></span>
                                </button>

                                {{-- Delete --}}
                                <form action="{{ route('admin.media.destroy', $image->id) }}" method="POST"
                                      onsubmit="return confirm('Permanently delete this image? This will break any notices using it.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-2.5 py-1.5 rounded-full text-[10px] font-semibold shadow"
                                            title="Delete">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>

                            {{-- Inline caption editor --}}
                            <form x-show="editing" style="display:none;"
                                  action="{{ route('admin.media.update', $image->id) }}" method="POST"
                                  class="mt-2 w-full px-3" @click.stop>
                                @csrf
                                @method('PATCH')
                                <div class="flex flex-col gap-1.5">
                                    <input name="caption" x-model="caption" type="text"
                                           placeholder="Add or update caption"
                                           class="w-full px-2 py-1.5 rounded-lg border border-white/20 bg-white/15 text-white text-xs placeholder-white/50 focus:outline-none focus:bg-white/25"
                                           @click.stop />
                                    <div class="flex gap-1.5">
                                        <button type="submit" class="flex-1 px-2 py-1.5 bg-[#e2a024] text-[#0b2415] rounded-lg text-xs font-bold">Save</button>
                                        <button type="button" @click.stop="editing=false" class="flex-1 px-2 py-1.5 bg-white/15 text-white rounded-lg text-xs">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="text-4xl mb-3 text-gray-300">🖼️</div>
                    <p class="text-gray-500 font-medium">Your gallery is empty.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $media->links() }}
        </div>
    </div>

    {{-- Lightbox --}}
    <div x-show="lbOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] bg-black/90 flex flex-col items-center justify-center"
         style="display:none;"
         @click="lbOpen = false">

        {{-- Close button --}}
        <button @click.stop="lbOpen = false"
                class="absolute top-4 right-4 flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 rounded-full text-white font-bold text-sm shadow-lg z-10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            Close
        </button>

        {{-- Image --}}
        <img :src="lbSrc"
             class="max-w-[90vw] max-h-[85vh] object-contain rounded-xl shadow-2xl"
             @click.stop>

        {{-- Caption --}}
        <template x-if="lbCaption">
            <p class="mt-4 text-white/80 text-sm font-medium text-center max-w-lg px-4" x-text="lbCaption"></p>
        </template>
    </div>

</div>
@endsection
