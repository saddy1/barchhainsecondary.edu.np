@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
])

<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" @required($required)
        {{ $attributes->merge(['class' => 'form-control']) }}>
</div>
