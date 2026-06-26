{{-- resources/views/components/media-manager.blade.php --}}
<div x-data="mediaManager()" 
     @open-media-manager.window="isOpen = true; request = $event.detail || {}; fetchMedia()"
     x-show="isOpen" 
     style="display: none;" 
     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 sm:p-6"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[80vh] flex flex-col overflow-hidden" @click.away="isOpen = false">
        
        {{-- Header & Tabs --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gray-50">
            <h3 class="text-xl font-bold text-gray-800">Media Library</h3>
            <button @click="isOpen = false" class="text-gray-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex border-b border-gray-200 px-6 pt-2 gap-6 bg-gray-50">
            <button @click="tab = 'upload'" :class="tab === 'upload' ? 'border-[#1a5632] text-[#1a5632]' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 border-b-2 font-bold text-sm transition-colors">Upload Files</button>
            <button @click="tab = 'library'" :class="tab === 'library' ? 'border-[#1a5632] text-[#1a5632]' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 border-b-2 font-bold text-sm transition-colors">Media Library</button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50">
            
            {{-- Tab 1: Upload --}}
            <div x-show="tab === 'upload'" class="h-full flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl bg-white">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <p class="text-gray-600 font-medium mb-4">Drag and drop your files here or click below</p>
                <input type="file" x-ref="fileInput" @change="uploadFile" class="hidden" accept="image/*">
                <button type="button" @click="$refs.fileInput.click()" class="px-6 py-2.5 bg-[#1a5632] text-white font-bold rounded-lg shadow-sm hover:bg-[#0b2415] transition-all" :disabled="isUploading">
                    <span x-show="!isUploading">Select File</span>
                    <span x-show="isUploading">Uploading...</span>
                </button>
            </div>

            {{-- Tab 2: Library Grid --}}
            <div x-show="tab === 'library'">
                <div x-show="isLoading" class="text-center py-10 text-gray-400">Loading images...</div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <template x-for="image in media" :key="image.id">
                        <div @click="selectedImage = image" 
                             :class="selectedImage && selectedImage.id === image.id ? 'ring-4 ring-[#1a5632] border-transparent scale-95' : 'border-gray-200 hover:border-[#e2a024]'"
                             class="relative aspect-square rounded-xl border-2 overflow-hidden cursor-pointer transition-all bg-white shadow-sm">
                            <img :src="image.url" class="w-full h-full object-cover">
                            
                            {{-- Checkmark for selected --}}
                            <div x-show="selectedImage && selectedImage.id === image.id" class="absolute top-2 right-2 bg-[#1a5632] text-white rounded-full p-1 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="media.length === 0 && !isLoading" class="text-center py-20 text-gray-500">
                    No media found. Upload some images first!
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="border-t border-gray-100 px-6 py-4 bg-white flex justify-end gap-3">
            <button type="button" @click="isOpen = false" class="px-5 py-2 text-gray-600 font-bold hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
            <button type="button" @click="insertImage()" :disabled="!selectedImage" :class="!selectedImage ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#0b2415] hover:shadow-lg'" class="px-6 py-2 bg-[#1a5632] text-white font-bold rounded-lg transition-all">
                Insert Selected Image
            </button>
        </div>
    </div>
</div>

<script>
function mediaManager() {
    return {
        isOpen: false,
        tab: 'library', // 'upload' or 'library'
        media: [],
        isLoading: false,
        isUploading: false,
        selectedImage: null,
        request: {},

        fetchMedia() {
            this.isLoading = true;
            fetch('{{ route('admin.media.index') }}')
                .then(res => res.json())
                .then(data => {
                    this.media = data;
                    this.isLoading = false;
                });
        },

        uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.isUploading = true;
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('admin.media.store') }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                this.isUploading = false;
                this.media.unshift(data.media); // Add new image to start of array
                this.tab = 'library'; // Switch to library view
                this.selectedImage = data.media; // Auto-select the newly uploaded image
                this.$refs.fileInput.value = ''; // Reset file input
            })
            .catch(error => {
                this.isUploading = false;
                alert('Upload failed. Please check file size and type.');
            });
        },

        insertImage() {
            if (!this.selectedImage) return;
            
            // Dispatch a custom event with the selected image URL
            window.dispatchEvent(new CustomEvent('image-selected', { 
                detail: {
                    image: this.selectedImage,
                    request: this.request
                }
            }));
            
            this.isOpen = false; // Close modal
        }
    }
}
</script>
