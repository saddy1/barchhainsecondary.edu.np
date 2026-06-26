@php
    $currentItem = $item ?? null;
    $items = $menu->items->where('id', '!=', $currentItem?->id);
@endphp
<div class="grid gap-2 md:grid-cols-2 xl:grid-cols-[1.2fr_1.2fr_1fr_1fr_1.2fr_1.4fr_.8fr_.7fr]">
    <input name="label" value="{{ old('label', $currentItem?->label) }}" required placeholder="Label" class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm font-bold">
    <input name="subtitle" value="{{ old('subtitle', $currentItem?->subtitle) }}" placeholder="Subtitle" class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm">
    <select name="parent_id" class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm">
        <option value="">No parent</option>
        @foreach($items as $parent)
            <option value="{{ $parent->id }}" @selected(old('parent_id', $currentItem?->parent_id) == $parent->id)>{{ $parent->label }}</option>
        @endforeach
    </select>
    <select name="type" class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm">
        <option value="page" @selected(old('type', $currentItem?->type ?? 'page') === 'page')>CMS Page</option>
        <option value="url" @selected(old('type', $currentItem?->type) === 'url')>Custom URL</option>
    </select>
    <select name="cms_page_id" class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm">
        <option value="">Select page</option>
        @foreach($pages as $page)
            <option value="{{ $page->id }}" @selected(old('cms_page_id', $currentItem?->cms_page_id) == $page->id)>{{ $page->title }}</option>
        @endforeach
    </select>
    <input name="url" value="{{ old('url', $currentItem?->url) }}" placeholder="/custom-url or https://..." class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm">
    <select name="target" class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm">
        <option value="_self" @selected(old('target', $currentItem?->target ?? '_self') === '_self')>Same tab</option>
        <option value="_blank" @selected(old('target', $currentItem?->target) === '_blank')>New tab</option>
    </select>
    <input type="number" name="sort_order" value="{{ old('sort_order', $currentItem?->sort_order ?? 0) }}" placeholder="Order" class="min-w-0 rounded-xl border border-gray-200 px-3 py-2 text-sm">
</div>
