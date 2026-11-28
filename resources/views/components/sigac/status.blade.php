@props([
    'label',
    'variant' => 'neutral',
])

<span {{ $attributes->class([
    'sigac-status',
    'sigac-status-' . $variant,
]) }}>

    {{ $label }}

</span>
