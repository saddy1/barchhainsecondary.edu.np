@php
    $isStaffEmployee = auth()->check() && !auth()->user()->isAdmin() && auth()->user()->device_id;
    $isAdmin         = auth()->check() && auth()->user()->isAdmin();
    $currentModule   = request()->is('admin/hr*') ? 'hr' : (request()->is('admin/id-card*') ? 'id-card' : (request()->is('admin/hajiri*') ? 'hajiri' : (request()->is('admin/learning*') ? 'learning' : 'website')));
    $user = auth()->user();
    $idCardUrl = route('card.dashboard');
    if ($user?->canAccess('students.view')) {
        $idCardUrl = route('students.index');
    } elseif ($user?->canAccess(['cards.view', 'cards.print'])) {
        $idCardUrl = route('bulk.index');
    } elseif ($user?->canAccess('students.card-request')) {
        $idCardUrl = route('admin.card-requests');
    } elseif ($user?->canAccess('card-settings.view')) {
        $idCardUrl = route('settings.index');
    }

    $moduleLinks = [
        ['key' => 'website',  'label' => 'Website',  'sub' => 'Public site',       'url' => route('admin.dashboard'), 'show' => $user?->canAccess(['dashboard.admin', 'dashboard.view', 'dashboard.financial'])],
        ['key' => 'hr',       'label' => 'HR',       'sub' => 'People master',     'url' => route('admin.hr.members.index'), 'show' => $user?->canAccess(['hr.members.view', 'hr.members.create', 'hr.members.edit', 'hr.members.delete']) && \App\Services\ModuleService::enabled('hr')],
        ['key' => 'id-card',  'label' => 'ID Card',  'sub' => 'Cards & students',  'url' => $idCardUrl, 'show' => $user?->canAccess(['students.view', 'students.create', 'students.edit', 'students.delete', 'users.bulk-import', 'cards.view', 'cards.print', 'students.card-request', 'card-settings.view'])],
        ['key' => 'hajiri',   'label' => 'Hajiri',   'sub' => 'Attendance',        'url' => route('hajiri.home'), 'show' => $user?->device_id || $user?->canAccess(['attendance.view', 'attendance.report', 'users.view', 'leaves.view', 'settings.view'])],
        ['key' => 'learning', 'label' => 'Learning', 'sub' => 'Courses & tests',   'url' => route('admin.learning.dashboard'), 'show' => $user?->canAccess(['learning.courses.view', 'learning.students.view', 'learning.lessons.view', 'learning.resources.view', 'learning.quizzes.view', 'learning.reports.view'])],
    ];
    $visibleModuleLinks = collect($moduleLinks)->filter(fn($module) => $module['show'])->values();
    $showModuleSwitcher = $visibleModuleLinks->isNotEmpty();

    // Notification counts — only computed for admins
    $notifLeaveReqs  = 0;
    $notifStaffCards = 0;
    $notifStudCards  = 0;
    $notifContacts   = 0;
    $notifTotal      = 0;

    if ($isAdmin) {
        try { $notifLeaveReqs  = \App\Models\Hajiri\LeaveRequest::where('status', 'pending')->count(); } catch (\Throwable) { $notifLeaveReqs  = 0; }
        try { $notifStaffCards = \App\Models\Hajiri\StaffCardRequest::where('status', 'pending')->count(); } catch (\Throwable) { $notifStaffCards = 0; }
        try { $notifStudCards  = \App\Models\Card\CardRequest::where('status', 'pending')->count(); } catch (\Throwable) { $notifStudCards  = 0; }
        try { $notifContacts   = \App\Models\ContactMessage::where('is_read', false)->count(); } catch (\Throwable) { $notifContacts   = 0; }
        $notifTotal      = $notifLeaveReqs + $notifStaffCards + $notifStudCards + $notifContacts;
    }

    // Per-module badge counts for the switcher tabs
    $moduleBadges = [
        'hajiri'  => $notifLeaveReqs + $notifStaffCards,
        'id-card' => $notifStudCards,
        'website' => $notifContacts,
        'hr' => 0,
        'learning' => 0,
    ];
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm shrink-0">
    <div class="flex items-center justify-between gap-2 sm:gap-3 px-3 sm:px-6 h-16 min-w-0">

        {{-- Mobile hamburger --}}
        <button @click="sidebarOpen = true"
                class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

            {{-- Brand + module switcher --}}
            <a href="{{ $isAdmin ? route('admin.dashboard') : ($visibleModuleLinks->first()['url'] ?? route('hajiri.home')) }}" class="hidden sm:flex items-center gap-2.5 shrink-0">
                <div class="w-8 h-8 bg-gray-50 border border-gray-200 rounded-lg p-1 flex items-center justify-center">
                    <img src="{{ $siteSettings->logoUrl() }}" alt="{{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }}" class="w-full h-full object-contain">
                </div>
                <div class="hidden md:block">
                    <p class="text-sm font-bold text-gray-900 leading-none">{{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-0.5">{{ $isAdmin ? 'Unified ERP Platform' : 'Staff Portal' }}</p>
                </div>
            </a>

            @if($showModuleSwitcher)
            <nav class="flex flex-1 sm:flex-none min-w-0 items-center gap-1 p-1 bg-gray-100 rounded-xl border border-gray-200 overflow-x-auto">
                @foreach($visibleModuleLinks as $m)
                @php $badge = $moduleBadges[$m['key']] ?? 0; @endphp
                <a href="{{ $m['url'] }}"
                   class="relative flex flex-col items-start px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all
                          {{ $currentModule === $m['key']
                              ? 'bg-[#1a5632] text-white shadow-sm'
                              : 'text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                    <span class="text-[13px] font-extrabold leading-tight">{{ $m['label'] }}</span>
                    <span class="hidden sm:block text-[10px] font-semibold opacity-70 leading-none mt-0.5">{{ $m['sub'] }}</span>
                    @if($badge > 0)
                    <span class="absolute -top-1 -right-1 min-w-4 h-4 px-0.5 bg-red-500 text-white text-[9px] font-extrabold rounded-full flex items-center justify-center leading-none">
                        {{ $badge > 99 ? '99+' : $badge }}
                    </span>
                    @endif
                </a>
                @endforeach
            </nav>
            @endif

        {{-- Right side: notification bell + user chip --}}
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">

            {{-- Notification Bell (admin only) --}}
            @if($isAdmin)
            <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
                <button @click="notifOpen = !notifOpen"
                        class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                        title="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($notifTotal > 0)
                    <span class="absolute top-0.5 right-0.5 min-w-4 h-4 px-0.5 bg-red-500 text-white text-[9px] font-extrabold rounded-full flex items-center justify-center leading-none">
                        {{ $notifTotal > 99 ? '99+' : $notifTotal }}
                    </span>
                    @endif
                </button>

                <div x-show="notifOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 mt-2 w-[calc(100vw-2rem)] sm:w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
                     style="display:none;">

                    {{-- Dropdown header --}}
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-extrabold text-gray-900">Notifications</p>
                            @if($notifTotal > 0)
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $notifTotal }} item{{ $notifTotal !== 1 ? 's' : '' }} need attention</p>
                            @else
                                <p class="text-[11px] text-green-600 font-semibold mt-0.5">All clear — nothing pending</p>
                            @endif
                        </div>
                        @if($notifTotal > 0)
                        <span class="w-7 h-7 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xs font-extrabold">
                            {{ $notifTotal > 99 ? '99+' : $notifTotal }}
                        </span>
                        @else
                        <span class="w-7 h-7 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        @endif
                    </div>

                    {{-- Notification items --}}
                    <div class="max-h-96 overflow-y-auto divide-y divide-gray-50">

                        {{-- ── Hajiri ── --}}
                        @if($notifLeaveReqs > 0 || $notifStaffCards > 0)
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[9px] font-extrabold text-gray-400 uppercase tracking-widest">Hajiri Module</p>
                        </div>
                        @endif

                        @if($notifLeaveReqs > 0)
                        <a href="{{ route('hajiri.leave-requests.index') }}" @click="notifOpen = false"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-amber-50 transition-colors group">
                            <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 group-hover:text-amber-700">Leave Requests</p>
                                <p class="text-[11px] text-gray-500">{{ $notifLeaveReqs }} pending approval</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-extrabold rounded-lg">{{ $notifLeaveReqs }}</span>
                        </a>
                        @endif

                        @if($notifStaffCards > 0)
                        <a href="{{ route('hajiri.staff-card-request.admin') }}" @click="notifOpen = false"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 transition-colors group">
                            <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-[#1a5632]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 group-hover:text-[#1a5632]">Staff ID Card Requests</p>
                                <p class="text-[11px] text-gray-500">{{ $notifStaffCards }} pending</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 bg-green-100 text-[#1a5632] text-xs font-extrabold rounded-lg">{{ $notifStaffCards }}</span>
                        </a>
                        @endif

                        {{-- ── ID Card Module ── --}}
                        @if($notifStudCards > 0)
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[9px] font-extrabold text-gray-400 uppercase tracking-widest">ID Card Module</p>
                        </div>
                        <a href="{{ route('admin.card-requests') }}" @click="notifOpen = false"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition-colors group">
                            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700">Student Card Requests</p>
                                <p class="text-[11px] text-gray-500">{{ $notifStudCards }} awaiting verification</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-extrabold rounded-lg">{{ $notifStudCards }}</span>
                        </a>
                        @endif

                        {{-- ── Website ── --}}
                        @if($notifContacts > 0)
                        <div class="px-4 pt-3 pb-1">
                            <p class="text-[9px] font-extrabold text-gray-400 uppercase tracking-widest">Website</p>
                        </div>
                        <a href="{{ route('contacts.index') }}" @click="notifOpen = false"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-purple-50 transition-colors group">
                            <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 group-hover:text-purple-700">Contact Messages</p>
                                <p class="text-[11px] text-gray-500">{{ $notifContacts }} unread</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-extrabold rounded-lg">{{ $notifContacts }}</span>
                        </a>
                        @endif

                        {{-- All clear --}}
                        @if($notifTotal === 0)
                        <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm font-extrabold text-gray-700">All caught up!</p>
                            <p class="text-xs text-gray-400 mt-1">No pending items across any module.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- User chip --}}
            <div class="flex items-center gap-2 sm:gap-2.5">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-[10px] text-gray-400 leading-none mt-0.5">{{ auth()->user()->role_label ?? ($isStaffEmployee ? 'Staff' : 'Admin') }}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-[#1a5632] text-white flex items-center justify-center text-sm font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-gray-600 border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
