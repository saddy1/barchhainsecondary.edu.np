@foreach($items as $item)
    <div class="relative group/cms">
        <a href="{{ $item->resolved_url }}" target="{{ $item->target }}"
           class="inline-flex items-center gap-1 text-[14px] font-medium text-gray-700 transition-all duration-200 hover:text-[#1a5632] hover:font-bold">
            {{ $item->label }}
            @if($item->children->isNotEmpty())
                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            @endif
        </a>
        @if($item->children->isNotEmpty())
            <div class="invisible absolute left-0 top-full w-56 pt-2 opacity-0 transition-all group-hover/cms:visible group-hover/cms:opacity-100" style="z-index: 100;">
                <div class="rounded-xl border border-gray-100 bg-white p-2 shadow-xl">
                    @include('partials.cms-menu-dropdown', ['items' => $item->children])
                </div>
            </div>
        @endif
    </div>
@endforeach
