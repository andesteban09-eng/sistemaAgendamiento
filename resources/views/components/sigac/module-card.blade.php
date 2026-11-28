@props(['title', 'description' => null, 'href', 'icon' => 'grid', 'color' => 'navy'])

<a href="{{ $href }}" {{ $attributes->class(['sigac-module']) }}>

    <div class="sigac-icon sigac-icon-{{ $color }}">
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    </div>

    <div class="sigac-module-content">

        <h3>{{ $title }}</h3>

        @if ($description)
            <p>{{ $description }}</p>
        @endif

    </div>

    <i class="bi bi-chevron-right sigac-module-arrow" aria-hidden="true"></i>

</a>
