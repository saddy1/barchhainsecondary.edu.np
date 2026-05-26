{{-- HR Sidebar --}}
<div x-show="sidebarOpen"
     x-transition.opacity
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-900/70 backdrop-blur-sm lg:hidden"
     style="display:none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       @click.capture="if ($event.target.closest('a')) sidebarOpen = false"
       class="fixed inset-y-0 left-0 z-50 w-60 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-dvh border-r shrink-0"
       style="background: linear-gradient(180deg, var(--theme-sidebar-bg, #0b2415) 0%, var(--theme-sidebar-gradient-end, #050f09) 100%); border-color: rgba(255,255,255,0.08);">
    <div class="flex items-center justify-between h-14 px-4 border-b shrink-0" style="border-color: rgba(255,255,255,0.08); background: rgba(0,0,0,0.25);">
        <a href="{{ route('admin.hr.members.index') }}" class="flex items-center gap-2.5 min-w-0">
            <div class="w-7 h-7 bg-white/90 rounded-lg flex items-center justify-center p-1 shrink-0">
                <img src="{{ $siteSettings->logoUrl() }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-white leading-none truncate">{{ $siteSettings->get('app_name', 'Barchhain ERP') }}</p>
                <p class="text-[9px] uppercase tracking-widest font-semibold mt-0.5" style="color: var(--theme-secondary, #e2a024);">HR Module</p>
            </div>
        </a>
        <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1 text-white/40 hover:text-white rounded-md hover:bg-white/10 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    @php
        $sidebarUser = auth()->user();
        $canHrView = $sidebarUser?->canAccess('hr.members.view');
        $canHrCreate = $sidebarUser?->canAccess('hr.members.create');
        $canHrEdit = $sidebarUser?->canAccess('hr.members.edit');
        $canCardSettings = $sidebarUser?->canAccess(['card-settings.view', 'card-settings.create', 'card-settings.edit']);
        $navActive = fn(string ...$patterns) => collect($patterns)->contains(fn ($pattern) => request()->routeIs($pattern) || request()->is($pattern));
    @endphp

    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5 custom-scrollbar">
        <p class="px-2 pt-1 pb-1.5 text-[10px] font-bold text-white/30 uppercase tracking-widest">People</p>

        @if($canHrView)
            <a href="{{ route('admin.hr.members.index') }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all {{ $navActive('admin.hr.members.index', 'admin/hr/members') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2a5 5 0 00-10 0v2m10 0H7m8-13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="flex-1 truncate">All People</span>
            </a>
        @endif

        @if($canHrCreate)
            <a href="{{ route('admin.hr.members.create') }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all {{ $navActive('admin.hr.members.create', 'admin/hr/members/create') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M12 4v16m8-8H4"/></svg>
                <span class="flex-1 truncate">New Member</span>
            </a>

            <a href="{{ route('admin.hr.members.import') }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all {{ $navActive('admin.hr.members.import', 'admin/hr/members/import') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span class="flex-1 truncate">Bulk Import</span>
            </a>
        @endif

        <p class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-white/30 uppercase tracking-widest">Setup</p>

        @if($canCardSettings)
            <a href="{{ route('settings.index', ['tab' => 'organizations', 'from' => 'hr']) }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->is('admin/id-card/settings*') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1"/></svg>
                <span class="flex-1 truncate">Organization Setup</span>
            </a>
            <a href="{{ route('settings.index', ['tab' => 'departments', 'from' => 'hr']) }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all {{ request('tab') === 'departments' ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span class="flex-1 truncate">Classes / Departments</span>
            </a>
            <a href="{{ route('settings.index', ['tab' => 'sections', 'from' => 'hr']) }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all {{ request('tab') === 'sections' ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 12h6M9 19h6M5 5h.01M5 12h.01M5 19h.01"/></svg>
                <span class="flex-1 truncate">Sections</span>
            </a>
            <a href="{{ route('settings.index', ['tab' => 'member_types', 'from' => 'hr']) }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all {{ request('tab') === 'member_types' ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 12h.01M7 17h.01M11 7h6M11 12h6M11 17h6"/></svg>
                <span class="flex-1 truncate">Member Types</span>
            </a>
        @endif

        @if($sidebarUser?->canAccess('students.view'))
            <p class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-white/30 uppercase tracking-widest">Linked Modules</p>
            <a href="{{ route('students.index') }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all text-white/60 hover:text-white hover:bg-white/8">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6h10M10 12h10M10 18h10M4 6h.01M4 12h.01M4 18h.01"/></svg>
                <span class="flex-1 truncate">ID Card Members</span>
            </a>
        @endif
    </nav>

    <div class="px-3 py-3 border-t shrink-0" style="border-color: rgba(255,255,255,0.08); background: rgba(0,0,0,0.3);">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center text-white text-xs font-bold border-2"
                 style="background-color: var(--theme-primary, #1a5632); border-color: var(--theme-secondary, #e2a024);">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-white truncate leading-tight">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-[10px] text-white/35 truncate leading-tight mt-0.5">{{ auth()->user()->role_label ?? 'Admin' }}</p>
            </div>
        </div>
    </div>
</aside>
