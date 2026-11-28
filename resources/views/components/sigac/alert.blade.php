@props([
    'variant' => 'info',
    'title' => null,
])

@php
    $icons = [
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'exclamation-circle',
        'info' => 'info-circle',
    ];

    $icon = $icons[$variant] ?? 'info-circle';
@endphp

<div {{ $attributes->class(['sigac-alert', 'sigac-alert-' . $variant]) }}
    role="{{ $variant === 'danger' ? 'alert' : 'status' }}">

    <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>

    <div>

        @if ($title)
            <strong>{{ $title }}</strong>
        @endif

        <div>{{ $slot }}</div>

    </div>

</div>
