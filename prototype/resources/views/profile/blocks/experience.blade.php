<div class="space-y-4">
    <h3 class="text-xs font-medium tracking-[0.2em] uppercase {{ $theme['labelText'] }}">
        {{ $block->data['heading'] ?? 'Experience' }}
    </h3>
    <ol class="space-y-4">
        @foreach ($block->data['items'] ?? [] as $item)
            <li class="border-l pl-4 {{ $theme['hairline'] }}">
                <p class="text-sm font-medium">
                    {{ $item['role'] ?? '' }}@if (! empty($item['company'])) · {{ $item['company'] }}@endif
                </p>
                @if (! empty($item['period']))
                    <p class="text-xs {{ $theme['mutedText'] }}">{{ $item['period'] }}</p>
                @endif
                @if (! empty($item['summary']))
                    <p class="mt-1 text-sm {{ $theme['bodyText'] }}">{{ $item['summary'] }}</p>
                @endif
            </li>
        @endforeach
    </ol>
</div>
