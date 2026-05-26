@php
    try {
        $adminNotices = \App\Models\Announcement::where('type', 'notice')
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
    } catch (\Throwable) {
        $adminNotices = collect();
    }
@endphp

@if($adminNotices->isNotEmpty())
<div class="overflow-hidden shrink-0 border-b" style="height:26px; background: linear-gradient(90deg, var(--theme-notice-bg, #0b2415) 0%, var(--theme-sidebar-gradient-end, #050f09) 100%); border-color: rgba(255,255,255,0.08);">
    <div class="flex items-center h-full">
        <div class="shrink-0 font-extrabold uppercase tracking-widest text-[9px] px-3 h-full flex items-center gap-1 z-10" style="background-color: var(--theme-notice-accent, #e2a024); color: var(--theme-dark, #0b2415);">
            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd"/>
            </svg>
            <span class="hidden sm:inline">Notices</span>
        </div>
        <div class="overflow-hidden flex-1 relative">
            <div class="admin-notice-ticker inline-flex items-center gap-8 sm:gap-10 px-4 sm:px-6 text-[11px]">
                @foreach($adminNotices as $n)
                    <a href="{{ route('admin.announcements.index') }}"
                       class="text-white/70 hover:text-white transition-colors flex items-center gap-1.5 min-h-0 whitespace-nowrap">
                        <span class="text-[8px]" style="color: var(--theme-notice-accent, #e2a024);">&#9679;</span>
                        {{ $n->title }}
                        <span class="text-white/30 text-[9px]">{{ $n->created_at?->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
