<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $title ?? 'Print Preview' }}</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #e5e7eb; }

#toolbar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    background: #1e3a5f; color: white;
    display: flex; align-items: center; gap: 12px;
    padding: 10px 20px; box-shadow: 0 2px 8px rgba(0,0,0,.3);
}
#toolbar h1 { font-size: 14px; font-weight: 600; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
#toolbar .meta { font-size: 12px; color: #93c5fd; white-space: nowrap; }
#toolbar button {
    border: none; cursor: pointer; border-radius: 6px;
    font-size: 13px; font-weight: 600; padding: 8px 18px;
    display: flex; align-items: center; gap: 6px;
}
.btn-print { background: #c8a951; color: #1e3a5f; }
.btn-print:hover:not(:disabled) { background: #e2c46a; }
.btn-print:disabled { opacity: .6; cursor: not-allowed; }
.btn-back  { background: rgba(255,255,255,.15); color: white; }
.btn-back:hover { background: rgba(255,255,255,.25); }
.btn-flip  { background: rgba(255,255,255,.15); color: white; }
.btn-flip:hover { background: rgba(255,255,255,.25); }
.btn-flip.active { background: #22c55e; color: #fff; }

#pages { margin-top: 60px; padding: 24px; display: flex; flex-direction: column; gap: 24px; align-items: center; }

.page-sheet {
    width: 1122px; height: 794px;
    background: white;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    border-radius: 3px;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}
.page-label { text-align: center; font-size: 11px; color: #6b7280; margin-top: 6px; }

.card-slot {
    position: absolute;
    width: 204px; height: 323px;
    overflow: hidden;
    outline: 1px dashed #d1d5db;
    background: #f9fafb;
}
.card-slot iframe {
    border: none; display: block;
    width: 204px; height: 323px;
    transform-origin: top left;
}

/* ── Pending spinner ── */
.card-slot.card-pending::before {
    content: '';
    position: absolute; inset: 0;
    background: #f3f4f6;
    z-index: 1;
    pointer-events: none;
}
.card-slot.card-pending::after {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    width: 26px; height: 26px;
    margin: -13px 0 0 -13px;
    border: 3px solid #d1d5db;
    border-top-color: #6b7280;
    border-radius: 50%;
    animation: spin .75s linear infinite;
    z-index: 2;
    pointer-events: none;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Card overlay (reload + remove) ── */
.card-overlay {
    display: none;
    position: absolute; inset: 0;
    background: rgba(0,0,0,.45);
    align-items: flex-start;
    justify-content: flex-end;
    gap: 4px;
    padding: 5px;
    z-index: 10;
}
.card-slot:hover .card-overlay { display: flex; }

.card-btn {
    border: none; cursor: pointer; border-radius: 5px;
    width: 28px; height: 28px;
    font-size: 13px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    transition: opacity .15s;
}
.card-btn:hover { opacity: .85; }
.card-btn-reload { background: #3b82f6; color: #fff; }
.card-btn-remove { background: #ef4444; color: #fff; }

/* ── Error state — overrides pending spinner via class specificity ── */
.card-slot.card-error::before { background: rgba(254,202,202,.25); }
.card-slot.card-error::after {
    content: '⚠ Failed to load';
    top: auto; left: 0; right: 0; bottom: 0;
    width: auto; height: auto;
    margin: 0;
    border: none; border-radius: 0;
    background: #ef4444; color: #fff;
    font-size: 10px; text-align: center; padding: 3px 0;
    animation: none;
}
.card-slot.card-error .card-overlay { display: flex; background: transparent; }

@media print {
    @page { size: A4 landscape; margin: 0; }
    #toolbar { display: none !important; }
    body { background: white; }
    #pages { margin-top: 0; padding: 0; gap: 0; }

    .page-sheet {
        width: 297mm; height: 210mm;
        box-shadow: none; border-radius: 0;
        page-break-after: always;
    }
    .page-sheet:last-child { page-break-after: auto; }
    .page-label { display: none; }

    .card-slot { outline: none; width: 54mm; height: 85.6mm; background: white; }
    .card-slot iframe { width: 54mm; height: 85.6mm; }
    .card-overlay { display: none !important; }
    .card-slot::before, .card-slot::after { display: none !important; }
}
</style>
</head>
<body>

<div id="toolbar">
    <button class="btn-back" onclick="goBack()">← Back</button>
    <h1>{{ $title }}</h1>
    <span class="meta" id="toolbarMeta">{{ count($cards) }} card(s) &middot; {{ ceil(count($cards) / $layout['per_page']) }} page(s)</span>
    <button class="btn-flip" id="flipBtn" onclick="toggleFlip()">↔&nbsp; Flip Photo</button>
    <button class="btn-print" id="printBtn" onclick="printAndMark()">🖨&nbsp; Print</button>
</div>

<div id="pages"></div>

@php
    $cardData = array_map(function ($c) {
        return [
            'src'   => $c['render_url'],
            'label' => ($c['student']->full_name ?? '') . ' — ' . ucfirst($c['type'] ?? ''),
        ];
    }, $cards);
@endphp

<script>
var allCards       = @json($cardData);
var studentIds     = @json($studentIds ?? []);
var markPrintedUrl = '{{ route('bulk.mark-printed') }}';
var csrfToken      = '{{ csrf_token() }}';
var flipped        = false;
var mmToPx         = 3.7795;

var layout = {
    cardW:   {{ $layout['card_w'] }},
    cardH:   {{ $layout['card_h'] }},
    marginX: {{ $layout['margin_x'] }},
    marginY: {{ $layout['margin_y'] }},
    gapX:    {{ $layout['gap_x'] }},
    gapY:    {{ $layout['gap_y'] }},
    cols:    {{ $layout['cols'] }},
    rows:    {{ $layout['rows'] }},
    perPage: {{ $layout['per_page'] }},
};

// ── Back button ───────────────────────────────────────────────────────────
// Preview is opened in a new tab — window.opener exists, so close the tab.
// Falls back to history.back() if navigated here directly.
function goBack() {
    if (window.opener && !window.opener.closed) {
        window.close();
    } else if (window.history.length > 1) {
        window.history.back();
    } else {
        window.close();
    }
}

// ── Deferred iframe loading via IntersectionObserver ─────────────────────
// Iframes store their src in data-src until they scroll into view.
var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
        if (entry.isIntersecting) {
            triggerLoad(entry.target);
            observer.unobserve(entry.target);
        }
    });
}, { rootMargin: '500px 0px' }); // start loading 500px before visible

function triggerLoad(slot) {
    var iframe = slot.querySelector('iframe');
    if (!iframe) return;
    var src = iframe.getAttribute('data-src');
    if (!src) return; // already loaded
    iframe.removeAttribute('data-src');
    iframe.src = src;
}

// ── Reflow: rebuild all pages from allCards array ─────────────────────────
function reflow() {
    observer.disconnect();

    var pagesDiv   = document.getElementById('pages');
    pagesDiv.innerHTML = '';

    var totalPages = Math.ceil(allCards.length / layout.perPage);
    if (totalPages === 0) {
        pagesDiv.innerHTML = '<p style="color:#6b7280;padding:40px;text-align:center;">No cards remaining.</p>';
        updateMeta(0, 0);
        return;
    }

    for (var p = 0; p < totalPages; p++) {
        var wrap  = document.createElement('div');
        var sheet = document.createElement('div');
        sheet.className  = 'page-sheet';
        sheet.dataset.page = p;

        var start = p * layout.perPage;
        var end   = Math.min(start + layout.perPage, allCards.length);
        for (var i = start; i < end; i++) {
            sheet.appendChild(buildSlot(i));
        }

        wrap.appendChild(sheet);

        if (totalPages > 1) {
            var lbl = document.createElement('p');
            lbl.className   = 'page-label';
            lbl.textContent = 'Page ' + (p + 1) + ' of ' + totalPages;
            wrap.appendChild(lbl);
        }

        pagesDiv.appendChild(wrap);
    }

    // Observe all slots after DOM is settled
    document.querySelectorAll('.card-slot').forEach(function (slot) {
        observer.observe(slot);
    });

    updateMeta(allCards.length, totalPages);
}

function buildSlot(globalIdx) {
    var card      = allCards[globalIdx];
    var posOnPage = globalIdx % layout.perPage;
    var col       = posOnPage % layout.cols;
    var row       = Math.floor(posOnPage / layout.cols);
    var xMm, yMm;
    if (allCards.length === 1) {
        // Center the single card on an A4 landscape page (297 × 210 mm)
        xMm = (297 - layout.cardW) / 2;
        yMm = (210 - layout.cardH) / 2;
    } else {
        xMm = layout.marginX + col * (layout.cardW + layout.gapX);
        yMm = layout.marginY + row * (layout.cardH + layout.gapY);
    }

    var slot = document.createElement('div');
    slot.className     = 'card-slot card-pending';
    slot.dataset.idx   = globalIdx;
    slot.dataset.xmm   = xMm;
    slot.dataset.ymm   = yMm;
    slot.title         = card.label;
    slot.style.left    = Math.round(xMm * mmToPx) + 'px';
    slot.style.top     = Math.round(yMm * mmToPx) + 'px';

    var src = card.src;
    if (flipped) src += (src.indexOf('?') !== -1 ? '&' : '?') + 'flip=1';

    var iframe = document.createElement('iframe');
    iframe.setAttribute('data-src', src); // deferred
    iframe.scrolling = 'no';
    iframe.title     = card.label;
    iframe.addEventListener('load', function () { onIframeLoad(this); });

    var overlay = document.createElement('div');
    overlay.className = 'card-overlay';

    var reloadBtn = document.createElement('button');
    reloadBtn.className   = 'card-btn card-btn-reload';
    reloadBtn.title       = 'Reload card';
    reloadBtn.textContent = '↺';
    reloadBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        reloadSlot(slot);
    });

    var removeBtn = document.createElement('button');
    removeBtn.className   = 'card-btn card-btn-remove';
    removeBtn.title       = 'Remove card';
    removeBtn.textContent = '✕';
    removeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        removeCard(globalIdx);
    });

    overlay.appendChild(reloadBtn);
    overlay.appendChild(removeBtn);
    slot.appendChild(iframe);
    slot.appendChild(overlay);
    return slot;
}

// ── Load event: clear spinner, flag errors ────────────────────────────────
function onIframeLoad(iframe) {
    var slot = iframe.closest('.card-slot');
    if (!slot) return;
    slot.classList.remove('card-pending');
    try {
        var body = iframe.contentDocument && iframe.contentDocument.body;
        if (!body || body.innerHTML.trim().length < 30) {
            slot.classList.add('card-error');
        } else {
            slot.classList.remove('card-error');
        }
    } catch (e) {
        slot.classList.remove('card-error'); // cross-origin — assume OK
    }
}

function reloadSlot(slot) {
    var iframe = slot.querySelector('iframe');
    if (!iframe) return;
    // Grab whichever holds the real URL
    var src = iframe.getAttribute('data-src') || iframe.src;
    if (!src || src === window.location.href) return;
    slot.classList.remove('card-error');
    slot.classList.add('card-pending');
    iframe.removeAttribute('data-src');
    iframe.src = '';
    setTimeout(function () { iframe.src = src; }, 60);
}

// ── Remove card and reflow ────────────────────────────────────────────────
function removeCard(idx) {
    allCards.splice(idx, 1);
    reflow();
}

// ── Flip Photo ────────────────────────────────────────────────────────────
function toggleFlip() {
    flipped = !flipped;
    var btn = document.getElementById('flipBtn');
    btn.classList.toggle('active', flipped);
    btn.textContent = flipped ? '↔ Flipped (ON)' : '↔ Flip Photo';

    document.querySelectorAll('.card-slot iframe').forEach(function (iframe) {
        var pendingSrc = iframe.getAttribute('data-src');
        if (pendingSrc !== null) {
            // Not yet loaded — update the stored src
            var s = pendingSrc.replace(/[?&]flip=1/, '').replace(/[?&]$/, '');
            if (flipped) s += (s.indexOf('?') !== -1 ? '&' : '?') + 'flip=1';
            iframe.setAttribute('data-src', s);
        } else if (iframe.src && iframe.src !== window.location.href) {
            // Already loaded — reload with/without flip param
            var s = iframe.src.replace(/[?&]flip=1/, '').replace(/[?&]$/, '');
            if (flipped) s += (s.indexOf('?') !== -1 ? '&' : '?') + 'flip=1';
            iframe.src = s;
        }
    });
}

// ── Update toolbar meta ───────────────────────────────────────────────────
function updateMeta(count, pages) {
    var el = document.getElementById('toolbarMeta');
    if (el) el.textContent = count + ' card(s) · ' + pages + ' page(s)';
}

// ── Force-load all pending iframes before printing ────────────────────────
function loadAllPending() {
    var promises = [];
    document.querySelectorAll('.card-slot iframe[data-src]').forEach(function (iframe) {
        promises.push(new Promise(function (resolve) {
            iframe.addEventListener('load',  resolve, { once: true });
            iframe.addEventListener('error', resolve, { once: true });
            var src = iframe.getAttribute('data-src');
            iframe.removeAttribute('data-src');
            iframe.src = src;
        }));
    });
    return promises.length ? Promise.all(promises) : Promise.resolve();
}

// ── Print + mark printed ──────────────────────────────────────────────────
function printAndMark() {
    var btn = document.getElementById('printBtn');
    btn.disabled    = true;
    btn.textContent = '⏳ Loading…';

    loadAllPending().then(function () {
        // Short settle delay for final renders
        setTimeout(function () {
            btn.disabled    = false;
            btn.textContent = '🖨 Print';
            window.print();

            if (studentIds.length > 0) {
                var body = new URLSearchParams();
                body.append('_token', csrfToken);
                studentIds.forEach(function (id) { body.append('student_ids[]', id); });
                fetch(markPrintedUrl, { method: 'POST', body: body }).catch(function () {});
            }
        }, 400);
    });
}

// ── Print layout: mm for print, px after ─────────────────────────────────
window.addEventListener('beforeprint', function () {
    document.querySelectorAll('.card-slot').forEach(function (slot) {
        slot.style.left   = slot.dataset.xmm + 'mm';
        slot.style.top    = slot.dataset.ymm + 'mm';
        slot.style.width  = '54mm';
        slot.style.height = '85.6mm';
        var iframe = slot.querySelector('iframe');
        if (iframe) { iframe.style.width = '54mm'; iframe.style.height = '85.6mm'; }
    });
});

window.addEventListener('afterprint', function () {
    document.querySelectorAll('.card-slot').forEach(function (slot) {
        slot.style.left   = Math.round(parseFloat(slot.dataset.xmm) * mmToPx) + 'px';
        slot.style.top    = Math.round(parseFloat(slot.dataset.ymm) * mmToPx) + 'px';
        slot.style.width  = '204px';
        slot.style.height = '323px';
        var iframe = slot.querySelector('iframe');
        if (iframe) { iframe.style.width = '204px'; iframe.style.height = '323px'; }
    });
});

// ── Initial render ────────────────────────────────────────────────────────
reflow();
</script>

</body>
</html>
