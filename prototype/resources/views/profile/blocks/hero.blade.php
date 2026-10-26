<div class="flex items-center gap-4">
    <div class="grid size-14 shrink-0 place-items-center rounded-full text-lg font-semibold {{ $theme['accentSurface'] }}">
        {{ mb_substr($block->data['heading'] ?? '?', 0, 1) }}
    </div>
    <div class="min-w-0">
        <h2 class="text-lg font-semibold sm:text-xl">{{ $block->data['heading'] ?? '' }}</h2>
        @if (! empty($block->data['body']))
            <p class="text-sm {{ $theme['mutedText'] }}">{{ $block->data['body'] }}</p>
        @endif
    </div>
</div>
