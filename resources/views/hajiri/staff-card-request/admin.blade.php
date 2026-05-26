@extends('hajiri.layouts.app')

@section('content')

{{-- Hero --}}
<div class="bg-[#1a5632] rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-[#e2a024] rounded-full blur-3xl opacity-20 pointer-events-none"></div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#e2a024] uppercase tracking-widest mb-1">Staff Services</p>
            <h2 class="text-2xl font-extrabold">Staff ID Card Requests</h2>
            <p class="text-green-200 text-sm mt-1">Review and manage staff ID card requests</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-center">
                <span class="block text-2xl font-black text-amber-300">{{ $requests->get('pending', collect())->count() }}</span>
                <span class="text-[10px] text-green-100 uppercase tracking-wide">Pending</span>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-center">
                <span class="block text-2xl font-black text-green-300">{{ $requests->get('approved', collect())->count() }}</span>
                <span class="text-[10px] text-green-100 uppercase tracking-wide">Approved</span>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-center">
                <span class="block text-2xl font-black text-blue-300">{{ $requests->get('printed', collect())->count() }}</span>
                <span class="text-[10px] text-green-100 uppercase tracking-wide">Printed</span>
            </div>
        </div>
    </div>
</div>

@if(session('message'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-4 font-medium">{{ session('message') }}</div>
@endif

{{-- Status update modal --}}
<div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="$('#statusModal').hide()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-extrabold text-gray-900">Update Request Status</h3>
            <button onclick="$('#statusModal').hide()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="statusForm" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">New Status</label>
                <select name="status" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632] bg-white">
                    <option value="approved">Approved</option>
                    <option value="printed">Printed</option>
                    <option value="collected">Collected</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Admin Note <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="admin_note" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#1a5632]" placeholder="e.g. Ready for collection at office">
            </div>
            <button type="submit" class="w-full py-2.5 bg-[#1a5632] hover:bg-[#0b2415] text-white text-sm font-extrabold rounded-xl transition-colors">
                Update Status
            </button>
        </form>
    </div>
</div>

{{-- Mobile cards --}}
<div class="block lg:hidden space-y-3">
    @forelse($all as $req)
    @php $c = $req->status_color; @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#1a5632] text-white text-sm font-extrabold flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($req->user->name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-sm">{{ $req->user->name ?? '—' }}</p>
                    <p class="text-[11px] text-gray-400">{{ $req->created_at->format('Y-m-d') }}</p>
                </div>
            </div>
            <span class="shrink-0 px-2.5 py-1 text-[10px] font-extrabold rounded-lg uppercase bg-{{ $c }}-100 text-{{ $c }}-700">{{ $req->status_label }}</span>
        </div>
        <p class="text-xs text-gray-600 mb-3">{{ $req->reason ?: '—' }}</p>
        @if($req->status === 'pending' || $req->status === 'approved' || $req->status === 'printed')
        <button type="button"
                onclick="$('#statusForm').attr('action','{{ route('hajiri.staff-card-request.status', $req->id) }}'); $('#statusModal').css('display','flex');"
                class="w-full py-2 bg-[#1a5632] hover:bg-[#0b2415] text-white text-xs font-extrabold rounded-xl transition-colors">
            Update Status
        </button>
        @endif
    </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <p class="text-sm font-extrabold text-gray-700">No requests yet</p>
        </div>
    @endforelse
</div>

{{-- Desktop table --}}
<div class="hidden lg:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($all->isEmpty())
        <div class="flex flex-col items-center justify-center py-16">
            <p class="text-base font-extrabold text-gray-700">No requests yet</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Staff Member</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Reason</th>
                    <th class="px-4 py-3 text-left text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Requested</th>
                    <th class="px-4 py-3 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-28">Status</th>
                    <th class="px-4 py-3 text-right text-[10px] font-extrabold text-gray-400 uppercase tracking-wider w-36">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($all as $req)
                @php $c = $req->status_color; @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-[#1a5632] text-white text-xs font-extrabold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($req->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $req->user->name ?? '—' }}</p>
                                <p class="text-[11px] text-gray-400">{{ $req->user->designation->label ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $req->reason ?: '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $req->created_at->format('Y-m-d') }}<br><span class="text-gray-400">{{ $req->created_at->diffForHumans() }}</span></td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg uppercase bg-{{ $c }}-100 text-{{ $c }}-700">{{ $req->status_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if(in_array($req->status, ['pending', 'approved', 'printed']))
                        <button type="button"
                                onclick="$('#statusForm').attr('action','{{ route('hajiri.staff-card-request.status', $req->id) }}'); $('#statusModal').css('display','flex');"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#1a5632] hover:bg-[#0b2415] text-white text-xs font-extrabold rounded-lg transition-colors">
                            Update
                        </button>
                        @else
                            <span class="text-xs text-gray-400">{{ $req->status_label }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $all->links() }}
    @endif
</div>

@endsection

@push('scripts')
<script>
    $(document).keydown(function(e) { if (e.key === 'Escape') $('#statusModal').hide(); });
</script>
@endpush
