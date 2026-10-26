<div class="space-y-4">
    <h3 class="text-xs font-medium tracking-[0.2em] uppercase {{ $theme['labelText'] }}">
        {{ $block->data['heading'] ?? 'Services' }}
    </h3>
    <ul class="grid gap-3 sm:grid-cols-2">
        @foreach ($block->data['services'] ?? [] as $service)
            <li class="rounded-xl border p-4 {{ $theme['hairline'] }}">
                <p class="text-sm font-medium">{{ $service['name'] ?? '' }}</p>
                <p class="mt-1 text-xs {{ $theme['mutedText'] }}">{{ $service['description'] ?? '' }}</p>
                @if (! empty($service['price']))
                    <p class="mt-2 text-xs font-medium {{ $theme['accentText'] }}">{{ $service['price'] }}</p>
                @endif
            </li>
        @endforeach
    </ul>
</div>
