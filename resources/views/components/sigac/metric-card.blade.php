@props(['label', 'value', 'icon' => 'bar-chart', 'color' => 'navy'])

<article {{ $attributes->class(['sigac-metric']) }}>

    <div class="sigac-metric-heading">

        <span>{{ $label }}</span>

        <div class="sigac-icon sigac-icon-{{ $color }}">
            <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
        </div>

    </div>

    <strong class="sigac-metric-value">
        {{ $value }}
    </strong>

</article>
