@props(['title', 'subtitle' => null, 'eyebrow' => 'SIGAC'])

<header {{ $attributes->class(['sigac-page-header']) }}>

    <div class="sigac-page-header-content">

        <span class="sigac-eyebrow">
            {{ $eyebrow }}
        </span>

        <h1>{{ $title }}</h1>

        @if ($subtitle)
            <p>{{ $subtitle }}</p>
        @endif

    </div>

    @isset($actions)
        <div class="sigac-page-header-actions">
            {{ $actions }}
        </div>
    @endisset

</header>
