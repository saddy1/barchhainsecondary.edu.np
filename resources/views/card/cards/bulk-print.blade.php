@extends('card.layouts.app')
@section('title', 'Bulk Print Cards')
@section('heading', 'Bulk Print Cards')

@section('content')

{{-- Two forms share the same student/card-type data.
     We collect data once and submit to whichever action the user clicks. --}}

<div class="grid grid-cols-3 gap-6">

    {{-- ── Left: Student selector ────────────────────────────────── --}}
    <div class="col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-primary">Select Members</h2>
                <div class="flex gap-2">
                    <button type="button" onclick="selectAll(true)"
                            class="text-xs text-blue-600 hover:underline">Select All</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="selectAll(false)"
                            class="text-xs text-gray-400 hover:underline">Clear</button>
                </div>
            </div>

            {{-- Filter tabs --}}
            <div class="flex gap-1 mb-4" id="filterTabs">
                @foreach(['all'=>'All', 'student'=>'Students', 'teacher'=>'Teachers', 'staff'=>'Staff'] as $val=>$label)
                <button type="button" data-filter="{{ $val }}"
                        class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium transition
                               {{ $val=='all' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto" id="studentList">
                @foreach($students as $student)
                <label class="student-row flex items-center gap-3 py-2.5 cursor-pointer hover:bg-gray-50 rounded-lg px-2 transition"
                       data-type="{{ $student->member_type }}">
                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                           class="accent-primary student-check">
                    <img src="{{ $student->photo_url }}" class="w-8 h-8 rounded-full object-cover bg-gray-100">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $student->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $student->roll_number }} · {{ ucfirst($student->member_type) }}</p>
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        <span class="text-xs bg-blue-50 text-blue-500 px-1.5 py-0.5 rounded">ID</span>
                        @if($student->has_library_card)
                            <span class="text-xs bg-emerald-50 text-emerald-500 px-1.5 py-0.5 rounded">Lib</span>
                        @endif
                        @if($student->has_bus_pass)
                            <span class="text-xs bg-orange-50 text-orange-500 px-1.5 py-0.5 rounded">Bus</span>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            <p class="text-xs text-gray-400 mt-3">
                <span id="selectedCount">0</span> member(s) selected
            </p>
        </div>
    </div>

    {{-- ── Right: Card types + Action buttons ────────────────────── --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="font-semibold text-primary mb-4">Card Types to Print</h2>
            <div class="space-y-3" id="cardTypeList">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="card_types[]" value="id" checked class="accent-primary card-type-check">
                    <div>
                        <p class="text-sm font-medium">🪪 ID Card</p>
                        <p class="text-xs text-gray-400">For all selected members</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="card_types[]" value="library" class="accent-primary card-type-check">
                    <div>
                        <p class="text-sm font-medium">📚 Library Card</p>
                        <p class="text-xs text-gray-400">Only for members with library card enabled</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="card_types[]" value="bus" class="accent-primary card-type-check">
                    <div>
                        <p class="text-sm font-medium">🚌 Bus Pass</p>
                        <p class="text-xs text-gray-400">Only for members with bus pass enabled</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-800 space-y-1">
            <p class="font-semibold">💡 Tip</p>
            <p>Use <strong>Print Preview</strong> to see the card layout in the browser and print directly, or <strong>Download PDF</strong> for a file you can save.</p>
            <p class="mt-1">10 cards fit per A4 landscape page.</p>
        </div>

        {{-- ── Action buttons ─────────────────────────────────────── --}}
        <div class="space-y-2">

            {{-- Print Preview button → opens in new tab --}}
            <button type="button" onclick="submitBulk('{{ route('bulk.preview') }}', '_blank')"
                    class="w-full bg-primary text-white py-3 rounded-xl font-semibold text-sm hover:bg-primary-light transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                🖨 Print Preview
            </button>

            {{-- Download PDF button --}}
            <button type="button" onclick="submitBulk('{{ route('bulk.generate') }}', '_self')"
                    class="w-full bg-white border border-gray-200 text-gray-700 py-3 rounded-xl font-semibold text-sm hover:bg-gray-50 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </button>
        </div>

    </div>
</div>

{{-- Hidden forms — one for preview (new tab), one for download --}}
<form id="bulkForm" method="POST" style="display:none;">
    @csrf
    <div id="hiddenStudents"></div>
    <div id="hiddenTypes"></div>
</form>

@endsection

@push('scripts')
<script>
// ── Count selected ────────────────────────────────────────────
function updateCount() {
    const n = document.querySelectorAll('.student-check:checked').length;
    document.getElementById('selectedCount').textContent = n;
}
document.querySelectorAll('.student-check').forEach(cb => {
    cb.addEventListener('change', updateCount);
});

// ── Select all / none ──────────────────────────────────────────
function selectAll(val) {
    document.querySelectorAll('.student-check:not([style*="display:none"])').forEach(cb => {
        const row = cb.closest('.student-row');
        if (!row || row.style.display !== 'none') cb.checked = val;
    });
    updateCount();
}

// ── Filter tabs ────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-primary', 'text-white');
            b.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
        });
        btn.classList.add('bg-primary', 'text-white');
        btn.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');

        const filter = btn.dataset.filter;
        document.querySelectorAll('.student-row').forEach(row => {
            row.style.display = (filter === 'all' || row.dataset.type === filter) ? '' : 'none';
        });
    });
});

// ── Build and submit the hidden form ───────────────────────────
function submitBulk(action, target) {
    const studentIds = [...document.querySelectorAll('.student-check:checked')].map(c => c.value);
    const cardTypes  = [...document.querySelectorAll('.card-type-check:checked')].map(c => c.value);

    if (studentIds.length === 0) {
        alert('Please select at least one member.');
        return;
    }
    if (cardTypes.length === 0) {
        alert('Please select at least one card type.');
        return;
    }

    const form = document.getElementById('bulkForm');
    form.action = action;
    form.target = target;

    // Build hidden inputs
    let studentsHTML = '';
    studentIds.forEach(id => {
        studentsHTML += `<input type="hidden" name="student_ids[]" value="${id}">`;
    });
    let typesHTML = '';
    cardTypes.forEach(t => {
        typesHTML += `<input type="hidden" name="card_types[]" value="${t}">`;
    });

    document.getElementById('hiddenStudents').innerHTML = studentsHTML;
    document.getElementById('hiddenTypes').innerHTML    = typesHTML;
    form.submit();
}
</script>
@endpush