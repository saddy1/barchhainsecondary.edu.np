{{-- resources/views/cards/all-cards.blade.php --}}
{{-- Renders ID Card + Library Card + Bus Pass for ONE student on one A4 page --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'DejaVu Sans', sans-serif; padding: 10mm; background: white; }
    h2  { font-size: 9pt; color: #666; margin-bottom: 4mm; text-align: center; border-bottom: 0.5px solid #ddd; padding-bottom: 2mm; }
    .card-row { display: flex; gap: 5mm; flex-wrap: wrap; margin-bottom: 6mm; }
    .section-title { font-size: 7pt; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2mm; }
    @page { margin: 0; }
</style>
</head>
<body>
    <h2>{{ $student->full_name }} — All Cards</h2>

    <div class="section-title">🪪 Identity Card</div>
    @include('card.cards.id-card', compact('student'))

    <br><br>

    @if($student->has_library_card)
    <div class="section-title">📚 Library Card</div>
    @include('card.cards.library-card', compact('student'))
    <br><br>
    @endif

    @if($student->has_bus_pass)
    <div class="section-title">🚌 Bus Pass</div>
    @include('card.cards.bus-pass', compact('student'))
    @endif
</body>
</html>
