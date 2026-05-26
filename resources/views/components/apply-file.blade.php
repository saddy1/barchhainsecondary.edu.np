@props([
    'label',
    'name',
    'accept' => '',
    'help' => '',
])

<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">{{ $label }} <span class="text-red-500">*</span></label>
    <input type="file" name="{{ $name }}" accept="{{ $accept }}" required
        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#1a5632] file:px-3 file:py-2 file:text-xs file:font-bold file:text-white focus:border-[#1a5632] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20">
    @if($help)
        <p class="text-xs text-gray-400 mt-1">{{ $help }}</p>
    @endif
    <div data-preview-for="{{ $name }}" class="mt-3 min-h-20 rounded-xl border border-dashed border-gray-200 bg-white p-3 text-xs text-gray-400">
        No file selected
    </div>
</div>
