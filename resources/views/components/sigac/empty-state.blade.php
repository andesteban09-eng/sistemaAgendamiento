@props([
    'title' => 'No hay registros',
    'description' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->class(['sigac-empty-state']) }}>

    <i class="bi bi-{{ $icon }}"
       aria-hidden="true"></i>

    <h3>{{ $title }}</h3>

    @if ($description)
        <p>{{ $description }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="sigac-empty-actions">
            {{ $slot }}
        </div>
    @endif

</div>
