@extends('hajiri.layouts.app')

@section('content')

{{-- Hero --}}
<div class="bg-[#1a5632] rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-[#e2a024] rounded-full blur-3xl opacity-20 pointer-events-none"></div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#e2a024] uppercase tracking-widest mb-1">Leave Management</p>
            <h2 class="text-2xl font-extrabold">Leave Requests</h2>
            <p class="text-green-200 text-sm mt-1">Review and approve employee leave applications</p>
        </div>
        <div class="flex flex-wrap gap-2 text-sm font-bold">
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-center">
                <span class="block text-2xl font-black text-amber-300">{{ $counts['pending'] }}</span>
                <span class="text-[10px] text-green-100 uppercase tracking-wide">Pending</span>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-center">
                <span class="block text-2xl font-black text-green-300">{{ $counts['approved'] }}</span>
                <span class="text-[10px] text-green-100 uppercase tracking-wide">Approved</span>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-center">
                <span class="block text-2xl font-black text-red-300">{{ $counts['rejected'] }}</span>
                <span class="text-[10px] text-green-100 uppercase tracking-wide">Rejected</span>
            </div>
        </div>
    </div>
</div>

@if (session('message'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-4">{{ session('message') }}</div>
@endif

{{-- Status Tabs --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5 mb-5 flex flex-wrap gap-1">
    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', '' => 'All'] as $val => $label)
        <a href="{{ route('hajiri.leave-requests.index', ['status' => $val]) }}"
           class="flex-1 min-w-0 text-center text-xs font-extrabold py-2 px-3 rounded-xl transition-colors
                  {{ $status == $val ? 'bg-[#1a5632] text-white shadow' : 'text-gray-500 hover:bg-gray-50' }}">
            {{ $label }}
            <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] {{ $status == $val ? 'bg-white/20' : 'bg-gray-100' }}">
                {{ $val === '' ? $counts['all'] : $counts[$val] }}
            </span>
        </a>
    @endforeach
</div>

{{-- Reject modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="$('#rejectModal').hide()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-extrabold text-gray-900">Reject Leave Request</h3>
            <button onclick="$('#rejectModal').hide()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="rejectForm" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Reason for rejection <span class="text-red-400">*</span></label>
                <textarea name="admin_remarks" rows="3" required
                          class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-red-400 resize-none"
                          placeholder="Explain why this leave is being rejected…"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-extrabold rounded-xl transition-colors">
                Confirm Rejection
            </button>
        </form>
    </div>
</div>

{{-- Mobile: cards --}}
<div class="block lg:hidden space-y-3">
    @forelse($requests as $req)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#1a5632] text-white text-sm font-extrabold flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($req->user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">{{ $req->user->name ?? '—' }}</p>
                    <p class="text-[11px] text-gray-400">{{ $req->user->designation->label ?? '—' }}</p>
                </div>
            </div>
            @php
                $colors = ['pending' => 'amber', 'approved' => 'green', 'rejected' => 'red'];
                $c = $colors[$req->status] ?? 'gray';
            @endphp
            <span class="shrink-0 px-2.5 py-1 text-[10px] font-extrabold rounded-lg uppercase bg-{{ $c }}-100 text-{{ $c }}-700">
                {{ $req->status }}
            </span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
            <div><p class="text-gray-400 font-bold uppercase tracking-wide text-[10px]">Leave Type</p><p class="font-semibold text-gray-700 mt-0.5">{{ $req->policy->name ?? '—' }}</p></div>
            <div><p class="text-gray-400 font-bold uppercase tracking-wide text-[10px]">Days</p><p class="font-extrabold text-[#1a5632] mt-0.5 text-base">{{ $req->days_count }}</p></div>
            <div><p class="text-gray-400 font-bold uppercase tracking-wide text-[10px]">From</p><p class="font-semibold text-gray-700 mt-0.5">{{ $req->start_date->format('Y-m-d') }}</p></div>
            <div><p class="text-gray-400 font-bold uppercase tracking-wide text-[10px]">To</p><p class="font-semibold text-gray-700 mt-0.5">{{ $req->end_date->format('Y-m-d') }}</p></div>
        </div>
        @if($req->reason)
            <p class="text-xs text-gray-500 mb-3 bg-gray-50 rounded-lg px-3 py-2">{{ $req->reason }}</p>
        @endif
        @if($req->status === 'pending')
        <div class="flex gap-2">
            <form method="POST" action="{{ route('hajiri.leave-requests.approve', $req->id) }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-2 bg-[#1a5632] hover:bg-[#0b2415] text-white text-xs font-extrabold rounded-xl transition-colors">
                    ✓ Approve
                </button>
            </form>
            <button type="button"
                    onclick="$('#rejectForm').attr('action','{{ route('hajiri.leave-requests.reject', $req->id) }}'); $('#rejectModal').css('display','flex');"
                    class="flex-1 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-extrabold rounded-xl border border-red-200 transition-colors">
                ✕ Reject
            </button>
        </div>
        @elseif($req->admin_remarks)
            <p class="text-[11px] text-gray-400 italic">Remarks: {{ $req->admin_remarks }}</p>
        @endif
    </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-16 text-center px-6">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm font-extrabold text-gray-700">No {{ $status ?: '' }} requests</p>
        </div>
    @endforelse
</div>

{{-- Desktop: table --}}
<div class="hidden lg:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($requests->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center px-6">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-base font-extrabold text-gray-700">No {{ $status ?: '' }} requests</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Employee</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Leave Type</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Period</th>
                    <th class="px-4 py-3 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-14">Days</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider hidden xl:table-cell">Reason</th>
                    <th class="px-4 py-3 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-24">Status</th>
                    <th class="px-4 py-3 text-right text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-48">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($requests as $req)
                @php
                    $colors = ['pending' => 'amber', 'approved' => 'green', 'rejected' => 'red'];
                    $c = $colors[$req->status] ?? 'gray';
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-[#1a5632] text-white text-xs font-extrabold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($req->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $req->user->name ?? '—' }}</p>
                                <p class="text-[11px] text-gray-400">{{ $req->user->designation->label ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800">{{ $req->policy->name ?? '—' }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ strtoupper($req->policy->short_code ?? '') }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-600">
                        {{ $req->start_date->format('Y-m-d') }}<br>
                        <span class="text-gray-400">to</span> {{ $req->end_date->format('Y-m-d') }}
                    </td>
                    <td class="px-4 py-3 text-center font-extrabold text-[#1a5632] text-base">{{ $req->days_count }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden xl:table-cell max-w-xs truncate">
                        {{ $req->reason ?: '—' }}
                        @if($req->admin_remarks)
                            <br><span class="text-red-400 italic">Remarks: {{ $req->admin_remarks }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg uppercase bg-{{ $c }}-100 text-{{ $c }}-700">
                            {{ $req->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($req->status === 'pending')
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('hajiri.leave-requests.approve', $req->id) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-extrabold rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Approve
                                </button>
                            </form>
                            <button type="button"
                                    onclick="$('#rejectForm').attr('action','{{ route('hajiri.leave-requests.reject', $req->id) }}'); $('#rejectModal').css('display','flex');"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 border border-red-200 hover:bg-red-100 text-red-600 text-xs font-extrabold rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">{{ $req->approvedBy->name ?? '' }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    $(document).keydown(function(e) { if (e.key === 'Escape') $('#rejectModal').hide(); });
</script>
@endpush
