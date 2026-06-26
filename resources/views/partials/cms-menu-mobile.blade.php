@foreach($items as $item)
    @if($item->children->isNotEmpty())
        <div x-data="{ open: false }">
            <button type="button" @click="open = !open" class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50">
                <span :class="open ? 'text-[#1a5632] font-bold' : ''">{{ $item->label }}</span>
                <svg :class="open ? 'rotate-180 text-[#1a5632]' : 'text-gray-400'" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition class="ml-4 space-y-1 border-l-2 border-green-100 py-1 pl-4 pr-2">
                @include('partials.cms-menu-mobile', ['items' => $item->children])
            </div>
        </div>
    @else
        <a href="{{ $item->resolved_url }}" target="{{ $item->target }}"
           class="block rounded-xl px-4 py-3 text-base font-medium text-gray-700 transition-all hover:bg-gray-50 hover:text-[#1a5632]">
            <span class="block">{{ $item->label }}</span>
            @if($item->subtitle)
                <span class="block text-xs font-medium text-gray-400">{{ $item->subtitle }}</span>
            @endif
        </a>
    @endif
@endforeach
