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
.btn-print:hover { background: #e2c46a; }
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

/* Portrait CR80 Card at 96dpi (54mm width x 85.6mm height) */
.card-slot {
    position: absolute;
    width: 204px; height: 323px; 
    overflow: hidden;
    outline: 1px dashed #d1d5db; /* Dotted line to show where the card is */
}
.card-slot iframe {
    border: none; display: block;
    width: 204px; height: 323px;
    transform-origin: top left;
}

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

    .card-slot { outline: none; width: 54mm; height: 85.6mm; }
    .card-slot iframe { width: 54mm; height: 85.6mm; }
}
</style>
</head>
<body>

<div id="toolbar">
    <button class="btn-back" onclick="window.history.length > 1 ? window.history.back() : window.close()">← Back</button>
    <h1>{{ $title }}</h1>
    <span class="meta">{{ count($cards) }} card(s) &middot; {{ ceil(count($cards) / $layout['per_page']) }} page(s)</span>
    <button class="btn-flip" id="flipBtn" onclick="toggleFlip()">↔&nbsp; Flip Photo</button>
    <button class="btn-print" onclick="window.print()">🖨&nbsp; Print</button>
</div>

<div id="pages">
@php
    $perPage = $layout['per_page'];
    $pages   = (int) ceil(count($cards) / $perPage);
    $mmToPx  = 3.7795;
@endphp

@for ($p = 0; $p < $pages; $p++)
<div>
<div class="page-sheet" data-page="{{ $p }}" style="position: relative;">
    @for ($i = $p * $perPage; $i < min(($p + 1) * $perPage, count($cards)); $i++)
    @php
        $card   = $cards[$i];
        $pos    = $layout['positions'][$i];
        $xPx    = round($pos['x'] * $mmToPx);
        $yPx    = round($pos['y'] * $mmToPx);
        $xMm    = $pos['x'];
        $yMm    = $pos['y'];
        $label  = ($card['student']->full_name ?? '') . ' — ' . ucfirst($card['type'] ?? '');
    @endphp
    <div class="card-slot" data-xmm="{{ $xMm }}" data-ymm="{{ $yMm }}" style="left: {{ $xPx }}px; top: {{ $yPx }}px;" title="{{ $label }}">
        <iframe src="{{ $card['render_url'] }}" loading="lazy" scrolling="no" title="{{ $label }}"></iframe>
    </div>
    @endfor
</div>
@if($pages > 1)
<p class="page-label">Page {{ $p + 1 }} of {{ $pages }}</p>
@endif
</div>
@endfor
</div>

<script>
var flipped = false;

function toggleFlip() {
    flipped = !flipped;
    var btn = document.getElementById('flipBtn');
    btn.classList.toggle('active', flipped);
    btn.textContent = flipped ? '↔ Flipped (ON)' : '↔ Flip Photo';

    document.querySelectorAll('.card-slot iframe').forEach(function (iframe) {
        var src = iframe.src.replace(/[?&]flip=1/, '').replace(/\?$/, '');
        if (flipped) {
            src += (src.includes('?') ? '&' : '?') + 'flip=1';
        }
        iframe.src = src;
    });
}

window.addEventListener('beforeprint', function () {
    document.querySelectorAll('.card-slot').forEach(function (slot) {
        var xmm = slot.dataset.xmm;
        var ymm = slot.dataset.ymm;
        slot.style.left   = xmm + 'mm';
        slot.style.top    = ymm + 'mm';
        slot.style.width  = '54mm';
        slot.style.height = '85.6mm';
        var iframe = slot.querySelector('iframe');
        if (iframe) { iframe.style.width = '54mm'; iframe.style.height = '85.6mm'; }
    });
});

window.addEventListener('afterprint', function () {
    var mmToPx = 3.7795;
    document.querySelectorAll('.card-slot').forEach(function (slot) {
        var xmm = parseFloat(slot.dataset.xmm);
        var ymm = parseFloat(slot.dataset.ymm);
        slot.style.left   = Math.round(xmm * mmToPx) + 'px';
        slot.style.top    = Math.round(ymm * mmToPx) + 'px';
        slot.style.width  = '204px';
        slot.style.height = '323px';
        var iframe = slot.querySelector('iframe');
        if (iframe) { iframe.style.width = '204px'; iframe.style.height = '323px'; }
    });
});
</script>

</body>
</html>