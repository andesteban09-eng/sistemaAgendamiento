@props([
    'title' => null,
    'subtitle' => null,
])

<section {{ $attributes->class(['sigac-card']) }}>

    @if ($title || $subtitle || isset($actions))

        <div class="sigac-card-heading">

            <div>

                @if ($title)
                    <h2>{{ $title }}</h2>
                @endif

                @if ($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif

            </div>

            @isset($actions)
                <div class="sigac-card-actions">
                    {{ $actions }}
                </div>
            @endisset

        </div>

    @endif

    {{ $slot }}

</section>
